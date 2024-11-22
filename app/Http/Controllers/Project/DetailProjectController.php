<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\DetailItemProject;
use App\Models\DetailProject;
use App\Models\Item;
use App\Models\Project;
use App\Models\ProjectType;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class DetailProjectController extends Controller
{
    public function create($id)
    {

        $data = [
            'tittle' => 'Detail Project',
            'item' => Item::all(),
            'types' => ProjectType::all(),
            'project' => Project::find($id)
        ];

        return view('pages.project.detail.add', $data);
    }


    public function getData(Request $request)
    {
        $projectid = $request->id;
        $dataType = DetailProject::with(['projecttype'])->where('project_id', $projectid)
            ->orderByDesc('id')
            ->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
                $button .= '<a href="' . route('project.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>';
                $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('projectdetail.delete', ['iddetail'=>$data->id,'id'=>$data->project_id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->editColumn('tipe', function ($data) {
                return $data->projecttype->name;
            })->rawColumns(['action'])
            ->make(true);
    }


    public function store(Request $request, $id)
    {
        $request->validate([
            'type_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'item_id' => 'required|array',
            'quantity' => 'required|array',
        ]);
    
        DB::beginTransaction(); 
    
        try {
           
            $project = DetailProject::create([
                'project_id' => $id,
                'type_project_id' => $request->type_id,
                'name' => $request->name,
                'description' => $request->description
            ]);
    
           
            $items = $request->item_id;
            $quantities = $request->quantity;
    
            foreach ($items as $index => $itemId) {
                DetailItemProject::create([
                    'detail_id' => $project->id,
                    'item_id' => $itemId,
                    'quantity' => $quantities[$index],
                ]);
            }
    
            DB::commit(); 
        } catch (Exception $e) {
            DB::rollBack(); 
            return redirect()->back()->with(['status' => 'Error', 'message' => 'Gagal Menambahkan Project Detail']);
        }
    
        return redirect()->route('project.detail', ['id' => $id])->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Project!']);
    }


    public function destroy(string $iddetail,string $id)
    {
        try {
            $data = DetailProject::find($id);
            //hapus data relasi ke detail item porject
            // $data->detailitemporject()->delete();
            $data->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Detail Projek Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Gagal Menghapus Data Detail Projek !",
                'trace' => $e->getTrace()
            ]);
        }
    }
}
