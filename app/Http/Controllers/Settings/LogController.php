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
        // Mengambil log activity yang terurut berdasarkan waktu terbaru
        $activities = Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($activities)
            ->addIndexColumn() // Menambahkan kolom index
            ->editColumn('causer', function ($data) {
                // Menampilkan nama pengguna yang melakukan aksi (causer)
                return $data->causer ? $data->causer->name : '-';
            })
            ->editColumn('description', function ($data) {
                // Menampilkan deskripsi aktivitas
                return $data->description;
            })
            ->editColumn('created_at', function ($data) {
                // Menampilkan waktu aktivitas yang terformat
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)
                    ->setTimezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s');
            })
            ->rawColumns(['causer', 'description', 'created_at']) // Kolom ini mengizinkan HTML
            ->make(true); // Mengembalikan data dalam format JSON untuk DataTables
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
