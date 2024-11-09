<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\TypeItem;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'tittle' => 'Item'
        ];

        return view('pages.master.item.index', $data);
    }

    public function getData(Request $request)
    {
        $dataType = Item::with(['type', 'unit'])
            ->orderByDesc('created_at')
            ->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('type', function ($item) {
                return $item->type ? $item->type->name : '-';  // Ensure correct access to typeItem
            })
            ->addColumn('unit', function ($item) {
                return $item->unit ? $item->unit->name : '-';  // Ensure correct access to unit
            })
            ->addColumn('action', function ($data) {
                $button = '';
                $button .= '<a href="' . route('item.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>';
                $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('item.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'type', 'unit'])
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'tittle' => 'Item',
            'units' => Unit::all(),
            'types' => TypeItem::all()
        ];

        return view('pages.master.item.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:type_items,id',
            'item_code' => 'required|unique:items,item_code',
            'unit_id' => 'required|exists:units,id',
            'material_price' => 'required|numeric',
            'service_price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // Simpan data item
        Item::create([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'item_code' => $request->item_code,
            'unit_id' => $request->unit_id,
            'material_price' => $request->material_price,
            'service_price' => $request->service_price,
            'description' => $request->description,
        ]);

        return redirect()->route('item');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'tittle' => 'Item',
            'item' => Item::findOrFail($id),
            'units' => Unit::all(),
            'types' => TypeItem::all()
        ];

        return view('pages.master.item.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:type_items,id',
            'item_code' => 'required|unique:items,item_code',
            'unit_id' => 'required|exists:units,id',
            'material_price' => 'required|numeric',
            'service_price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $item = Item::findOrFail($id);

        $item->update([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'item_code' => $request->item_code,
            'unit_id' => $request->unit_id,
            'material_price' => $request->material_price,
            'service_price' => $request->service_price,
            'description' => $request->description,
        ]);

        return redirect()->route('item');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = Item::findOrFail($id);
            $data->delete();

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
