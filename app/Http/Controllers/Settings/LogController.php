<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class LogController extends Controller
{
    public function index()
    {
        $data = [
            'user' => User::all(),
            'tittle' => 'Log Activity',
        ];
        return view('pages.settings.log.index', $data);
    }
    public function getData()
    {
        $activity = Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($activity)
            ->addIndexColumn()
            ->editColumn('user', function ($data) {
                return $data->causer ? $data->causer->name : 'System';
            })
            ->editColumn('action', function ($data) {
                return $this->getActionLabel($data->event);
            })
            ->editColumn('module', function ($data) {
                return $this->getModuleName($data->subject_type);
            })
            ->editColumn('description', function ($data) {
                return $this->generateDescription($data);
            })
            ->editColumn('created_at', function ($data) {
                return Carbon::parse($data->created_at)->format('d/m/Y H:i:s');
            })
            ->rawColumns(['description'])
            ->make(true);
    }

    private function getActionLabel($event)
    {
        return match ($event) {
            'created' => 'Tambah',
            'updated' => 'Update',
            'deleted' => 'Hapus',
            'login' => 'Login',
            'logout' => 'Logout',
            default => ucfirst($event)
        };
    }

    private function getModuleName($type)
    {
        if (!$type) return 'System';

        // Ambil nama class terakhir dari namespace
        $parts = explode('\\', $type);
        return end($parts);
    }

    private function generateDescription($data)
    {
        $user = $data->causer ? $data->causer->name : 'System';
        $action = $this->getActionLabel($data->event);
        $module = $this->getModuleName($data->subject_type);

        // Untuk event login/logout
        if (in_array($data->event, ['login', 'logout'])) {
            return "User <strong>$user</strong> melakukan $action ke sistem";
        }

        // Untuk event CRUD
        $properties = json_decode($data->properties, true);
        $attributes = $properties['attributes'] ?? [];

        // Jika ada nama/identifikasi dari item yang dimodifikasi
        $itemName = $attributes['name'] ?? $attributes['username'] ?? $attributes['email'] ?? '';
        $itemInfo = $itemName ? " ($itemName)" : '';

        return "User <strong>$user</strong> melakukan $action pada $module$itemInfo";
    }


    public function cleanlog()
    {
        try {
            Activity::truncate();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Log Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }
}
