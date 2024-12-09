<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DetailItemProject;
use App\Models\DetailProject;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectReview;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class ProjectReportController extends Controller
{
    public function index(Request $request){
        
        $project_id = $request->query('project_id');
        $project = Project::find($project_id);
        $data = [
            'tittle' => "Report Project $project->name",
            'project' => Project::with(['company','vendor','summary','responsibleperson','projectlisence','Projectfile'])->where('id',$project_id)->first(),
            'id'=>$project_id
        ];
        return view('pages.report.projectreport.index', $data);
    }


    public function getDataReview(Request $request)
    {
        $project_id = $request->query('project_id');
        $dataReview = ProjectReview::where('project_id',$project_id)->get();
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



    public function DetailProjectReport(Request $request){
        $project_id = $request->input('project_id');
        $DetailProject = DetailProject::with(['detailitemporject','project','projecttype'])->where('project_id',$project_id)->get();
        return DataTables::of($DetailProject)
        ->addIndexColumn()
        ->addColumn('action', function ($data) {
            $button = '';
            $button .= '<button class="btn btn-sm btn-success action" data-id="' . $data->id . '" data-type="view"  data-route="' . route('report.project.getdetailitem', ['id'=>$data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Detail Item">
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
        ->rawColumns(['action','project','type'])
        ->make(true);
    }


    public function DetailItem(Request $request,$id){
        
        
        $item = DetailItemProject::with(['detail','item'])->where('detail_id',$id)->get();
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
        ->rawColumns(['item_name','material_price','service_price','item_code'])
        ->make(true);
    }

}
