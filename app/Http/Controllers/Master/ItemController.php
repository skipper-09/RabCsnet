<?php

namespace App\Http\Controllers\Master;

use App\Exports\ItemExport;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\TypeItem;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Facades\Excel;
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
            ->editColumn('material_price', function ($data) {
                return $data->material_price ? 'Rp ' . number_format($data->material_price, 2, ',', '.') : '-';
            })
            ->editColumn('service_price', function ($data) {
                return $data->service_price ? 'Rp ' . number_format($data->service_price, 2, ',', '.') : '-';
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-items')) {
                    $button .= '<a href="' . route('item.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }
                if ($userauth->can('delete-items')) {
                    $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('item.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                    <i class="fas fa-trash-alt"></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'type', 'unit','service_price'])
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
            'unit_id' => 'required|exists:units,id',
            'material_price' => 'required|numeric',
            'service_price' => 'required|numeric',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'type_id.required' => 'Tipe wajib diisi.',
            'type_id.exists' => 'Tipe tidak valid.',
            'unit_id.required' => 'Satuan wajib diisi.',
            'unit_id.exists' => 'Satuan tidak valid.',
            'material_price.required' => 'Harga material wajib diisi.',
            'material_price.numeric' => 'Harga material harus berupa angka.',
            'service_price.required' => 'Harga Jasa wajib diisi.',
            'service_price.numeric' => 'Harga Jasa harus berupa angka.',
            'description.string' => 'Deskripsi wajib berupa string.',
        ]);

        // Simpan data item
        $item = Item::create($request->all());

        // Log activity for company creation
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($item) // The entity being changed
            ->event('created') // Event of the action
            ->withProperties([
                'attributes' => $item->toArray() // The data that was created
            ])
            ->log('Item dibuat dengan nama ' . $item->name);

        return redirect()->route('item')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Item']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'tittle' => 'Item',
            'item' => Item::find($id),
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
            'unit_id' => 'required|exists:units,id',
            'material_price' => 'required|numeric',
            'service_price' => 'required|numeric',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'type_id.required' => 'Tipe wajib diisi.',
            'type_id.exists' => 'Tipe tidak valid.',
            'unit_id.required' => 'Satuan wajib diisi.',
            'unit_id.exists' => 'Satuan tidak valid.',
            'material_price.required' => 'Harga material wajib diisi.',
            'material_price.numeric' => 'Harga material harus berupa angka.',
            'service_price.required' => 'Harga Jasa wajib diisi.',
            'service_price.numeric' => 'Harga Jasa harus berupa angka.',
            'description.string' => 'Deskripsi wajib berupa string.',
        ]);

        // Simpan data item
        $item = Item::find($id);
        $oldItem = $item->toArray();

        $item->update($request->all());

        // Log activity for company update
        activity()
            ->causedBy(Auth::user())
            ->performedOn($item)
            ->event('updated')
            ->withProperties([
                'old' => $oldItem, // The data before update
                'attributes' => $item->toArray() // The updated data
            ])
            ->log('Item di update dengan nama ' . $item->name);

        return redirect()->route('item')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Item']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);
            $itemData = $item->toArray(); // Capture the data before deletion

            $item->delete();

            // Log activity for d$data deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($item) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $itemData // The data before deletion
                ])
                ->log('Item dihapus dengan nama ' . $item->name);

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



    public function ExportItem()
    {
        $now = now();
        return Excel::download(new ItemExport, "Item_$now.csv");
    }
}
