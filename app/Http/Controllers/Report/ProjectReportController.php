<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProjectReportController extends Controller
{
    public function index(Request $request){
        // dd($request->query('project_id'));
        $projectId = $request->query('project_id'); // Ambil project_id dari query string
        $selectedProject = $projectId ? Project::find($projectId) : null;
    
        $data = [
            'tittle' => 'Report Project',
            'projects' => Project::all(),
            'selectedProject' => $selectedProject, // Project yang dipilih
        ];
        return view('pages.report.projectreport.index', $data);
    }


    public function getDataFile(Request $request)
    {
        $project_id = $request->input('project_id');
        $dataType = ProjectFile::where('project_id',$project_id)->get();
        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
                // $review = ProjectReview::where('project_id', $data->id)->orderByDesc('id')->first();
                $button .= '<a href="' . route('project.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>';
                $button .= '<a href="' . route('project.detail', $data->id) . '" class="btn btn-sm btn-warning action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-eye"></i>
            </a>';
                $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('project.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


}
