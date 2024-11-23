<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProjectController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Project'
        ];

        return view('pages.project.index', $data);
    }

    public function getData(Request $request)
    {
        $dataType = Project::with(['company', ])
            ->orderByDesc('id')
            ->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
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
            ->editColumn('company',function($data){
                return $data->company->name;
            })->rawColumns(['action','company'])
            ->make(true);
    }


    public function detail($id)
    {
        
        $data = [
            'tittle' => 'Detail Project',
            'project'=>Project::find($id),
        ];

        return view('pages.project.detail', $data);
    }

    public function create()
    {
        $data = [
            'tittle' => 'Project',
            'company'=>Company::all(),
            'user'=> User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Developer')->orwhere('name','Vendor');
            })->get(),
            'vendor'=> Vendor::all()
        ];

        return view('pages.project.add', $data);
    }


    public function store(Request $request){
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        $project = Project::create($request->all());
        return redirect()->route('project.detail',['id'=>$project->id])->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Project!']);
    }


    public function destroy(string $id)
    {
        try {
            $data = Project::find($id);
            $data->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Projek Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Gagal Menghapus Data Projek !",
                'trace' => $e->getTrace()
            ]);
        }
    }

}
