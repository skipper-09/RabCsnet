<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'tittle' => 'Tipe Project'
        ];

        return view('pages.master.projecttype.index', $data);
    }


    public function getData(Request $request)
    {
        $datatype = ProjectType::orderByDesc('id')->get();

        return DataTables::of($datatype)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-projecttypes')) {
                $button .= ' <a href="' . route('projecttype.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                class="fas fa-pencil-alt"></i></a>';
            }
            if ($userauth->can('delete-projecttypes')) {
                $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('projecttype.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                class="fas fa-trash-alt "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'tittle' => 'Tipe Project'
        ];

        return view('pages.master.projecttype.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        $projectType = ProjectType::create($request->all());

        //  Log activity for Project Type creation
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($projectType) // The entity being changed
            ->event('created') // Event of the action
            ->withProperties([
                'attributes' => $projectType->toArray() // The data before deletion
            ])
            ->log('Project Tipe dibuat dengan nama ' . $projectType->name);
        return redirect()->route('projecttype')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Tipe Project']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = [
            'tittle' => 'Tipe Project',
            'projecttype' => ProjectType::find($id),
        ];

        return view('pages.master.projecttype.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Nama wajib diisi.',
        ]);
        $projecttype = ProjectType::find($id);
        $projecttype->update($request->all());

        // Log activity for Project Type update
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($projecttype) // The entity being changed
            ->event('updated') // Event of the action
            ->withProperties([
                'attributes' => $projecttype->toArray() // The data before deletion
            ])
            ->log('Project Tipe ' . $projecttype->name . ' diperbarui');

        return redirect()->route('projecttype')->with(['status' => 'Success', 'message' => 'Berhasil Update Tipe Project']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $projectType = ProjectType::findOrFail($id);
            $projectTypeData = $projectType->toArray();

            $projectType->delete();

            // Log activity for Project Type deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($projectType) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $projectTypeData // The data before deletion
                ])
                ->log('Project Tipe ' . $projectType->name . ' dihapus');

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data !',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
