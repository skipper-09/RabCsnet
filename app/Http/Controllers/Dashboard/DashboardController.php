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
        $currentUserRole = $currentUser->roles->first()?->name;

        // Hitung jumlah proyek yang belum direview
        $totalProjectsNotReviewed = Project::whereHas("ProjectReview", function ($query) {
            $query->where("status_review", "pending");
        })->count();

        // Hitung jumlah proyek yang sudah selesai
        $totalCompletedProjects = Project::where('status', 'finish')->count();

        // Tentukan proyek yang perlu direview berdasarkan role
        $projectsToReview = 0; // Default 0
        switch ($currentUserRole) {
            case 'Developer':
                // Developer memiliki akses penuh untuk melihat semua proyek yang perlu direview
                $projectsToReview = Project::whereHas('ProjectReview', function ($query) {
                    $query->whereIn('status_review', ['pending', 'in_review']);
                })->count();
                break;

            case 'Accounting':
                // Hitung proyek yang belum direview oleh Accounting
                $projectsToReview = Project::whereDoesntHave('ProjectReview', function ($query) {  // Pastikan memeriksa relasi ProjectReview
                    $query->where('status_review', 'pending')  // Cek status review pada project_reviews
                        ->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Accounting');  // Pastikan reviewer adalah Accounting
                        });
                })->count();
                break;

            case 'Owner':
                // Hitung proyek yang belum direview oleh Owner
                $projectsToReview = Project::whereDoesntHave('ProjectReview', function ($query) {  // Pastikan memeriksa relasi ProjectReview
                    $query->whereIn('status_review', ['in_review', 'pending'])  // Cek status review pada project_reviews
                        ->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Owner');  // Pastikan reviewer adalah Owner
                        });
                })->count();
                break;

            default:
                // Role lain tidak memiliki akses untuk menghitung proyek yang perlu direview
                $projectsToReview = 0;
                break;
        }

        // Ambil data proyek dengan status 'in_progres'
        $projectTableDt = Project::with(['company', 'detailproject', 'Projectfile', 'ProjectReview'])
            ->where('status', 'in_progres')
            ->orderByDesc('id')
            ->get();

        // Hitung jumlah proyek berdasarkan status
        $projectall = Project::count();
        $projectcomplate = Project::where('status', 'finish')->count();
        $projectinprogres = Project::where('status', 'in_progres')->count();
        $projectpending = Project::where('status', 'pending')->count();

        // Hitung rata-rata progress proyek
        $projectprogres = Project::with(['taskdata'])->where('status', 'in_progres')
            ->orderByDesc('id')
            ->get();
        $progressCollection = $projectprogres->map(function ($proyek) {
            return $proyek->progress();
        });
        $averageProgress = $progressCollection->avg();

        // Data untuk view default
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

        // Vendor
        if ($currentUserRole == 'Vendor') {
            $vendor_id = Vendor::where('user_id', $currentUser->id)->value('id');
            $projectCount = Project::where('vendor_id', $vendor_id)->count();
            $taskCounts = Task::where('vendor_id', $vendor_id)
                ->selectRaw("COUNT(*) as total_tasks, 
                         SUM(CASE WHEN status = 'in_progres' THEN 1 ELSE 0 END) as pending_tasks,
                         SUM(CASE WHEN status = 'complated' THEN 1 ELSE 0 END) as finished_tasks")
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

        // Project Manager atau Waspam
        else if ($currentUserRole == 'Project Manager' || $currentUserRole == 'Waspam' || $currentUserRole == 'Admin PM') {
            $projectCount = Project::whereHas('ProjectReview', function ($query) {
                $query->where('status_review', 'approved');  // Memeriksa status_review di project_reviews
            })->whereHas('Projectfile')  // Memeriksa apakah proyek memiliki file terkait
            ->count();
            
            $taskCounts = Task::selectRaw("COUNT(*) as total_tasks, 
                                      SUM(CASE WHEN status = 'in_progres' THEN 1 ELSE 0 END) as pending_tasks, 
                                      SUM(CASE WHEN status = 'complated' THEN 1 ELSE 0 END) as finished_tasks")
                ->first();

            $data = [
                'tittle' => 'Dashboard',
                'project' => $projectCount,
                'taskall' => $taskCounts->total_tasks ?? 0,
                'taskfinish' => $taskCounts->finished_tasks ?? 0,
                'taskpending' => $taskCounts->pending_tasks ?? 0,
                'projectall' => $projectall,
                'projeccomplate' => $projectcomplate,
                'projectinprogres' => $projectinprogres,
                'projectpending' => $projectpending,
                'projectprogress' => $averageProgress,
            ];

            return view('pages.dashboard.projectmanagerdashboard', $data);
        }

        return view('pages.dashboard.index', $data);
    }

    public function getData(Request $request)
    {
        $dataType = Project::with(['company', 'detailproject', 'Projectfile', 'ProjectReview', 'responsibleperson', 'taskdata'])->where('status', 'in_progres')
            ->orderByDesc('id')->limit(4)
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
            })->editColumn('company', function ($data) {
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
            ->editColumn('name', function ($data) {
                return $data->vendor_id != null ? '<a href="' . route('report.project', ['project_id' => $data->id]) . '" class="text-primary "> ' . $data->name . '</a>' : $data->name;
            })
            ->rawColumns(['action', 'company', 'status', 'responsible_person', 'progress', 'end_date', 'name'])
            ->make(true);
    }
}
