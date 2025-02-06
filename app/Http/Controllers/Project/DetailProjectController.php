<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\DetailItemProject;
use App\Models\DetailProject;
use App\Models\Item;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetailProjectController extends Controller
{
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
                $button .= '<a href="' . route('projectdetail.edit', ['iddetail' => $data->id, 'id' => $data->project_id]) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>';
                $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('projectdetail.delete', ['iddetail' => $data->id, 'id' => $data->project_id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->editColumn('tipe', function ($data) {
                return $data->projecttype->name;
            })->rawColumns(['action'])
            ->make(true);
    }

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

    public function store(Request $request, $id)
    {
        $request->validate([
            'type_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'item_id' => 'required|array',
            'service_id' => 'nullable|array',
            'material' => 'nullable|array',
            'quantity' => 'required|array',
        ], [
            'type_id.required' => 'Tipe Projek Wajib di isi',
            'name.required' => 'Nama Wajib di isi',
            'description.required' => 'Deskripsi Wajib di isi',
            'quantity.required' => 'Jumlah Wajib di isi',
        ]);

        DB::beginTransaction();

        try {
            
            // insert detail project
            $project = DetailProject::create([
                'project_id' => $id,
                'type_project_id' => $request->type_id,
                'name' => $request->name,
                'description' => $request->description
            ]);

            $items = $request->item_id;
            $services = $request->service_id ?? [];
            $materials = $request->material ?? [];
            $quantities = $request->quantity;
            

            foreach ($items as $index => $itemId) {
                $itemall = Item::find($itemId);


                $serviceId = null;
                $costService = 0;
                $costMaterial = 0;

                if (!empty($services[$index])) {
                    $service = $services[$index];
                    if ($service == "on") {
                        $costService = $itemall->service_price;
                    }else{
                        $costService = 0;
                    }
                }

                if (!empty($materials[$index])) {
                    $material = $materials[$index];
                    if ($material == "on") {
                        $costMaterial = $itemall->material_price;
                    }else{
                        $costMaterial = 0;
                    }
                }

                // Create detail item project
                DetailItemProject::create([
                    'detail_id' => $project->id,
                    'item_id' => $itemId,
                    'service_id' => $serviceId,
                    'quantity' => $quantities[$index],
                    'cost_material' => $costMaterial * $quantities[$index],
                    'cost_service' => $costService * $quantities[$index]
                ]);
            }

            DB::commit();

            return redirect()->route('project.detail', ['id' => $id])
                ->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Project!']);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with(['status' => 'Error', 'message' => 'Gagal Menambahkan Project Detail: ' . $e->getMessage()]);
        }
    }

    public function show($id, $iddetail)
    {
        $detail = DetailProject::find($iddetail);
        $data = [
            'tittle' => 'Detail Project',
            'item' => Item::all(),
            'types' => ProjectType::all(),
            'project' => Project::find($id),
            'detailproject' => $detail,
            'projectDetails' => $detail->detailitemporject
        ];
        

        return view('pages.project.detail.edit', $data);
    }

    public function update(Request $request, $id, $iddetail)
    {
        // dd($request->all());
        $request->validate([
            'type_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'item_id' => 'required|array',
            'material' => 'required|array',
            'service_id' => 'nullable|array',
            'quantity' => 'required|array',
        ], [
            'type_id.required' => 'Tipe Projek Wajib di isi',
            'name.required' => 'Nama Wajib di isi',
            'description.required' => 'Deskripsi Wajib di isi',
            'quantity.required' => 'Jumlah Wajib di isi',
        ]);

        DB::beginTransaction();

        try {
            $project = DetailProject::findOrFail($iddetail);
            $project->update([
                'project_id' => $id,
                'type_project_id' => $request->type_id,
                'name' => $request->name,
                'description' => $request->description
            ]);

            // Hapus detail item project yang lama
            DetailItemProject::where('detail_id', $iddetail)->delete();

            $items = $request->item_id;
            $services = $request->service_id ?? [];
            $materials = $request->material ?? [];
            $quantities = $request->quantity;

            foreach ($items as $index => $itemId) {
                // dd($itemId);
                $itemall = Item::find($itemId);

                $serviceId = null;
                $costService = 0;
                $costMaterial = 0;

                if (!empty($services[$index])) {
                    $service = $services[$index];
                    if ($service == "on") {
                        $costService = $itemall->service_price;
                    }else{
                        $costService = 0;
                    }
                }

                if (!empty($materials[$index])) {
                    $material = $materials[$index];
                    if ($material == "on") {
                        $costMaterial = $itemall->material_price;
                    }else{
                        $costMaterial = 0;
                    }
                }

                // Create detail item project
                DetailItemProject::create([
                    'detail_id' => $project->id,
                    'item_id' => $itemId,
                    'service_id' => $serviceId,
                    'quantity' => $quantities[$index],
                    'cost_material' =>$costMaterial* $quantities[$index],
                    'cost_service' => $costService * $quantities[$index]
                ]);
            }

            DB::commit();

            return redirect()->route('project.detail', ['id' => $id])
                ->with(['status' => 'Success', 'message' => 'Berhasil Memperbarui Project!']);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with(['status' => 'Error', 'message' => 'Gagal Memperbarui Project Detail: ' . $e->getMessage()]);
        }
    }


    public function destroy(string $iddetail, string $id)
    {
        try {
            $data = DetailProject::find($id);
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
