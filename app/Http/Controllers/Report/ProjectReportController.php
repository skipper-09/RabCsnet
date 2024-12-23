<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DetailItemProject;
use App\Models\DetailProject;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectReview;
use App\Models\Task;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class ProjectReportController extends Controller
{
    public function index(Request $request)
    {

        $project_id = $request->query('project_id');
        $project = Project::find($project_id);

        // chart task
        $statusData = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->where('project_id', $project_id)
            ->pluck('count', 'status')
            ->toArray();

        $totalTasks = array_sum($statusData);

       
        $statusLabels = [
            'pending' => 'Pending Tasks',
            'in_progres' => 'In Progress',
            'complated' => 'Completed',
            'overdue' => 'Overdue Tasks'
        ];

        $chartData = [];
        $allStatuses = ['pending', 'in_progres', 'complated', 'overdue'];
        foreach ($allStatuses as $status) {
            $count = $statusData[$status] ?? 0;
            $chartData[$status] = $totalTasks > 0 ? round(($count / $totalTasks) * 100, 2) : 0; // Dalam persentase
        }

        $customLabels = [];
        foreach ($chartData as $status => $percentage) {
            $customLabels[] = $statusLabels[$status] ?? $status;
        }

        //remaining days
        $now = Carbon::now()->toDateString();
        $endDate = Carbon::parse($project->end_date);

        if ($endDate) {
            $remainingDays = $endDate->diffInDays($now);
        } else {
            $remainingDays = 0;
        }


        $projectprogres = Project::with(['taskdata'])->where('status', 'in_progres')
            ->orderByDesc('id')
            ->get();
        $progressCollection = $projectprogres->map(function ($proyek) {
            return $proyek->progress();
        });
        $averageProgress = $progressCollection->avg();



        $statuses = [
            // 'pending' => 'To Do',
            'in_progres' => 'In Progress',
            'complated' => 'Completed',  // Fixed typo here
        ];

        // Grouping tasks for Kanban view
        $kanbanTasks = Task::whereNull('parent_id')
            ->where('project_id', $project_id)  // Add this line to filter by project_id
            ->with('project')
            ->get()
            ->groupBy('status');


        $data = [
            'tittle' => "Report Project $project->name",
            'project' => Project::with(['company', 'vendor', 'summary', 'responsibleperson', 'projectlisence', 'Projectfile'])->where('id', $project_id)->first(),
            'id' => $project_id,
            'chartData' => $chartData,
            'customLabels' => $customLabels,
            'progres' => $project->progress(),
            'remainingdays' => $remainingDays,
            'statuses' => $statuses,
            'kanbanTasks' => $kanbanTasks,
            'totaltask' => $totalTasks,
            'percentace' => $averageProgress
        ];
        return view('pages.report.projectreport.index', $data);
    }


    public function getDataReview(Request $request)
    {
        $project_id = $request->query('project_id');
        $dataReview = ProjectReview::where('project_id', $project_id)->get();
        return DataTables::of($dataReview)
            ->addIndexColumn()
            ->addColumn('project', function ($item) {
                return $item->project ? $item->project->name : '-';
            })
            ->addColumn('reviewer', function ($item) {
                return $item->reviewer ? $item->reviewer->name : '-';
            })
            ->addColumn('status_pengajuan', function ($item) {
                $status = $item->project->status_pengajuan;

                switch ($status) {
                    case 'pending':
                        return '<span class="badge bg-warning">Pending</span>';
                    case 'in_review':
                        return '<span class="badge bg-primary">In Review</span>';
                    case 'approved':
                        return '<span class="badge bg-success">Approved</span>';
                    case 'Rejected':
                        return '<span class="badge bg-danger">Rejected</span>';
                    default:
                        return '<span class="badge bg-secondary">Unknown</span>';
                }
            })
            ->editColumn('review_date', function ($data) {
                return Carbon::parse($data->created_at)->format('Y-m-d');
            })
            ->rawColumns(['project', 'reviewer', 'status_pengajuan', 'review_date'])
            ->make(true);
    }



    public function DetailProjectReport(Request $request)
    {
        $project_id = $request->input('project_id');
        $DetailProject = DetailProject::with(['detailitemporject', 'project', 'projecttype'])->where('project_id', $project_id)->get();
        return DataTables::of($DetailProject)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
                $button .= '<button class="btn btn-sm btn-success action" data-id="' . $data->id . '" data-type="view"  data-route="' . route('report.project.getdetailitem', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Detail Item">
            <i class="fas fa-eye"></i>
        </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->addColumn('project', function ($item) {
                return $item->project ? $item->project->name : '-';
            })
            ->addColumn('type', function ($item) {
                return $item->projecttype->name;
            })
            ->rawColumns(['action', 'project', 'type'])
            ->make(true);
    }


    public function DetailItem(Request $request, $id)
    {


        $item = DetailItemProject::with(['detail', 'item'])->where('detail_id', $id)->get();
        return DataTables::of($item)
            ->addIndexColumn()
            ->addColumn('item_name', function ($item) {
                return $item->item->name;
            })
            ->addColumn('item_code', function ($item) {
                return $item->item->item_code;
            })
            ->addColumn('material_price', function ($item) {
                return formatRupiah($item->item->material_price);
            })
            ->addColumn('service_price', function ($item) {
                return formatRupiah($item->item->service_price);
            })
            ->addColumn('total', function ($item) {
                // Hitung total: (material_price + service_price) * quantity
                $total = ($item->item->material_price + $item->item->service_price) * $item->quantity;
                return formatRupiah($total); // Format total sebagai mata uang
            })
            ->rawColumns(['item_name', 'material_price', 'service_price', 'item_code', 'total'])
            ->make(true);
    }
}
