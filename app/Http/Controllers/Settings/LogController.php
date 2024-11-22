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
        $activity = Activity::with('causer')->orderBy('created_at', 'desc')->get();
        return DataTables::of($activity)
            ->addIndexColumn()
            ->editColumn('causer', function ($data) {
                return $data->causer == null ? '-' : $data->causer->name;
            })
            ->editColumn('created_at', function ($data) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)
                    ->setTimezone(config('app.timezone'))
                    ->format('Y-m-d H:i:s');
            })
            ->editColumn('description', function ($data) {
                return $this->generateAuditDescription($data);
            })
            ->rawColumns(['causer', 'created_at', 'description'])
            ->make(true);
    }
    
    private function generateAuditDescription($data)
{
    // Mengabaikan log jika causer adalah developer
    if ($data->causer_type === 'developer') {
        return null; // Tidak perlu menampilkan log
    }

    $properties = json_decode($data->properties, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'Error decoding properties';
    }

    $old = $properties['old'] ?? [];
    $new = $properties['attributes'] ?? [];

    $descriptions = [];
    foreach ($new as $key => $newValue) {
        $oldValue = $old[$key] ?? null;

        // Periksa apakah field adalah boolean dan ubah value-nya menjadi 'Aktif' atau 'Tidak Aktif'
        if (in_array($key, ['is_active', 'status'])) {
            $newValue = $newValue == 1 ? 'Aktif' : 'Tidak Aktif';
            $oldValue = $oldValue == 1 ? 'Aktif' : ($oldValue === null ? 'N/A' : 'Tidak Aktif');
        } else {
            // Jika bukan boolean, atur nilai default untuk N/A
            $newValue = $newValue ?? 'N/A';
            $oldValue = $oldValue ?? 'N/A';
        }

        $fieldLabel = ucwords(str_replace('_', ' ', $key));

        if ($data->event === 'updated') {
            if ($this->hasValueChanged($oldValue, $newValue)) {
                $descriptions[] = "$data->log_name $fieldLabel diubah dari <strong>$oldValue</strong> menjadi <strong>$newValue</strong>";
            }
        } elseif ($data->event === 'created') {
            $descriptions[] = "$data->log_name $fieldLabel dibuat dengan nilai <strong>$newValue</strong>";
        } elseif ($data->event === 'deleted') {
            if ($oldValue !== 'N/A') {
                $descriptions[] = "$data->log_name $fieldLabel dihapus dengan nilai <strong>$oldValue</strong>";
            }
        }
    }

    if (!empty($descriptions)) {
        return implode('<br>', $descriptions);
    }

    return 'Tidak ada perubahan yang terdeteksi';
}

    
    private function hasValueChanged($oldValue, $newValue)
    {
        $oldValueString = is_array($oldValue) ? json_encode($oldValue) : (string)$oldValue;
        $newValueString = is_array($newValue) ? json_encode($newValue) : (string)$newValue;
    
        return $oldValueString !== $newValueString;
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
