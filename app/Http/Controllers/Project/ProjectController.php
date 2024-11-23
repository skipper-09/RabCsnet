<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DetailProject;
use App\Models\Project;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
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
        $dataType = Project::with(['company',])
            ->orderByDesc('id')
            ->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
                $button .= '<a href="' . route('project.proses', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Proses Pengajuan">
                <i class="fas fa-upload"></i> Proses Pengajuan
            </a>';
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
            ->editColumn('company', function ($data) {
                return $data->company->name;
            })->rawColumns(['action', 'company'])
            ->make(true);
    }


    public function detail($id)
    {

        $data = [
            'tittle' => 'Detail Project',
            'project' => Project::find($id),
        ];

        return view('pages.project.detail', $data);
    }

    public function create()
    {
        $data = [
            'tittle' => 'Project',
            'company' => Company::all(),
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Developer')->orwhere('name', 'Vendor');
            })->get(),
            'vendor' => Vendor::all()
        ];

        return view('pages.project.add', $data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'company_id.required' => 'Perusahaan wajib diisi.',
            'company_id.exists' => 'Perusahaan tidak valid.',
        ]);

        $project = Project::create($request->all());

        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($project) // The entity being changed
            ->event('created') // Event of the action
            ->log('Project dibuat dengan nama ' . $project->name);

        return redirect()->route('project.detail', ['id' => $project->id])->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Project!']);
    }

    public function show($id)
    {
        $data = [
            'tittle' => 'Project',
            'project' => Project::find($id),
            'company' => Company::all(),
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Developer')->orwhere('name', 'Vendor');
            })->get(),
            'vendor' => Vendor::all()
        ];

        return view('pages.project.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'company_id.required' => 'Perusahaan wajib diisi.',
            'company_id.exists' => 'Perusahaan tidak valid.',
        ]);
        $project->update($request->all());

        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($project) // The entity being changed
            ->event('updated') // Event of the action
            ->withProperties([
                'attributes' => $project->toArray() // The data before deletion
            ])
            ->log('Project di update dengan nama ' . $project->name);
        return redirect()->route('project')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Project!']);
    }


    public function destroy(string $id)
    {
        try {
            $project = Project::find($id);
            $projectData = $project->toArray();

            $project->delete();

            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($project) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $projectData // The data before deletion
                ])
                ->log('Project dihapus dengan nama ' . $project->name);

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






    public function ProsesProject($id)
    {
        $project = Project::find($id);
        $detailProjects = DetailProject::with(['detailitemporject'])->where('project_id', $id)->get();
        $ppnRate = 0.11;
        // Olah data untuk menghitung total biaya material dan service
        $detailData = $detailProjects->map(function ($detail) use($ppnRate) {
            $totalMaterial = 0;
            $totalService = 0;

            
            foreach ($detail->detailitemporject as $detailItem) {
                // Ambil biaya material dan service dari item terkait
                $totalMaterial += $detailItem->cost_material;
                $totalService += $detailItem->cost_service;
            }

            $subTotal = $totalMaterial + $totalService;
            $ppn = $subTotal * $ppnRate;
            $totalWithPpn = $subTotal + $ppn;

            return [
                'distribusi' => $detail->name,
                'total_material' => $totalMaterial,
                'total_service' => $totalService,
                'total'=>$subTotal,
                'ppn' => $ppn,
                'total_with_ppn' => $totalWithPpn,
            ];
        });

        



        $data = [
            'tittle' => $project->name,
            'project' => $detailProjects,
            'details' => $detailData,
        ];
        return view('pages.project.proses', $data);
    }
}
