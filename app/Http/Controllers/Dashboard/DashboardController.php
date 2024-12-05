<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectReview;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        // Dapatkan pengguna saat ini
        $currentUser = Auth::user();

        // Dapatkan role pengguna saat ini
        $currentUserRole = $currentUser->roles->first()?->name; // Menggunakan null coalescing operator


        // Hitung jumlah proyek yang belum direview
        $totalProjectsNotReviewed = Project::where('status_pengajuan', 'pending')->count();

        // Hitung jumlah proyek yang sudah selesai
        $totalCompletedProjects = Project::where('status', 'finish')->count();

        // Tentukan proyek yang perlu direview berdasarkan role
        $projectsToReview = 0; // Default 0
        switch ($currentUserRole) {
            case 'Accounting':
                $projectsToReview = Project::where('status_pengajuan', 'pending')
                    ->whereDoesntHave('ProjectReview', function ($query) {
                        $query->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Accounting');
                        });
                    })->count();
                break;

            case 'Owner':
                $projectsToReview = Project::where('status_pengajuan', 'pending')
                    ->whereDoesntHave('ProjectReview', function ($query) {
                        $query->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Owner');
                        });
                    })->count();
                break;

            case 'Developer':
                $projectsToReview = Project::whereIn('status_pengajuan', ['pending', 'in_review'])
                    ->whereHas('Projectfile')
                    ->count(); // Tambahkan count() untuk menghitung jumlah proyek
                break;

            default:
                // Role lain tidak memiliki akses
                break;
        }

        $projectTableDt = Project::with(['company', 'detailproject', 'Projectfile', 'ProjectReview'])->where('status', 'in_progres')
            ->orderByDesc('id')
            ->get();

        $projectall = Project::all()->count();
        $projectcomplate = Project::where('status', 'finish')->get()->count();
        $projectinprogres = Project::where('status', 'in_progres')->get()->count();
        $projectpending = Project::where('status', 'pending')->get()->count();

        $projectprogres = Project::with(['taskdata'])->where('status', 'in_progres')
            ->orderByDesc('id')
            ->get();
        $progressCollection = $projectprogres->map(function ($proyek) {
            return $proyek->progress();
        });
        $averageProgress = $progressCollection->avg();


        // Data yang akan diteruskan ke view
        $data = [
            'tittle' => 'Dashboard',
            'totalProjectsNotReviewed' => $totalProjectsNotReviewed,
            'totalCompletedProjects' => $totalCompletedProjects,
            'projectsToReview' => $projectsToReview,
            'project' => $projectTableDt,
            'projectall' => $projectall,
            'projeccomplate' => $projectcomplate,
            'projectinprogres' => $projectinprogres,
            'projectpending' => $projectpending,
            'projectprogress' => $averageProgress,
        ];


        if ($currentUserRole == 'Vendor') {
            $vendor_id = Vendor::where('user_id', $currentUser->id)->value('id'); // Mengambil hanya nilai ID
            $projectCount = Project::where('vendor_id', $vendor_id)->count();
            $taskCounts = Task::where('vendor_id', $vendor_id)
                ->selectRaw("
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = 'in_progres' THEN 1 ELSE 0 END) as pending_tasks,
                    SUM(CASE WHEN status = 'complated' THEN 1 ELSE 0 END) as finished_tasks
                ")
                ->first();

            $data = [
                'tittle' => 'Dashboard',
                'project' => $projectCount,
                'taskall' => $taskCounts->total_tasks ?? 0,
                'taskfinish' => $taskCounts->finished_tasks ?? 0,
                'taskpending' => $taskCounts->pending_tasks ?? 0,
            ];

            return view('pages.dashboard.vendordashboard', $data);
        }

        return view('pages.dashboard.index', $data);
    }



    public function getData(Request $request)
    {
        $dataType = Project::with(['company', 'detailproject', 'Projectfile', 'ProjectReview', 'responsibleperson', 'taskdata'])->where('status', 'in_progres')
            ->orderByDesc('id')
            ->get();


        return DataTables::of($dataType)
            ->addIndexColumn()
            ->editColumn('status', function ($data) {
                $status = '';

                if ($data->status == 'pending') {
                    $status = '<span class="badge badge-pill badge-soft-primary font-size-13">Pending</span>';
                } else if ($data->status == 'in_progres') {
                    $status = '<span class="badge badge-pill badge-soft-info font-size-13">In Progress</span>';
                } else if ($data->status == 'canceled') {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-13">Canceled</span>';
                } else {
                    $status = '<span class="badge badge-pill badge-soft-success font-size-13">Selesai</span>';
                }

                return $status;
            })->editColumn('status_pengajuan', function ($data) {
                $status_pengajuan = '';

                if ($data->status_pengajuan == 'pending') {
                    $status_pengajuan = '<span class="badge badge-pill badge-soft-primary font-size-13">Pending</span>';
                } else if ($data->status_pengajuan == 'in_review') {
                    $status_pengajuan = '<span class="badge badge-pill badge-soft-info font-size-13">In Review</span>';
                } else if ($data->status_pengajuan == 'approved') {
                    $status_pengajuan = '<span class="badge badge-pill badge-soft-success font-size-13">Approved</span>';
                } else if ($data->status_pengajuan == 'revision') {
                    $status_pengajuan = '<span class="badge badge-pill badge-soft-warning font-size-13">Revision</span>';
                } else {
                    $status_pengajuan = '<span class="badge badge-pill badge-soft-danger font-size-13">Rejected</span>';
                }
                return $status_pengajuan;
            })
            ->editColumn('company', function ($data) {
                return $data->company->name;
            })->editColumn('end_date', function ($data) {
                return Carbon::parse($data->end_date)->format('d-m-Y');
            })->editColumn('responsible_person', function ($data) {
                return $data->responsibleperson->name ?? '-';
            })->editColumn('progress', function ($data) {
                $tes = '';

                $tes = '<input data-plugin="knob" data-width="40" data-height="40" data-linecap=round
                                                    data-fgColor="#34c38f" value=' . $data->progress() . ' data-skin="tron" 
                                                    data-readOnly=true  />
                                            ';
                return $tes;
            })
            ->rawColumns(['action', 'company', 'status', 'responsible_person', 'progress','end_date'])
            ->make(true);
    }
}
