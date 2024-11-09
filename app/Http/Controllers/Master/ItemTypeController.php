<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TypeItem;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'tittle' => 'Item Type'
        ];

        return view('pages.master.itemtype.index', $data);
    }


    public function getData(Request $request)
    {
        $datatype = TypeItem::orderByDesc('created_at')->get();

        return DataTables::of($datatype)->addIndexColumn()->addColumn('action', function ($data) {
            $button = '';
            $button .= ' <a href="' . route('itemtype.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            class="fas fa-pencil-alt"></i></a>';
            $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('itemtype.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                            class="fas fa-trash-alt "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'tittle' => 'Item Type'
        ];

        return view('pages.master.itemtype.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TypeItem $typeItem)
    {
        $request->validate([
            'name'=>'required',
        ]);

        $typeItem->insert([
            'name'=>$request->name
        ]);

        return redirect()->route('itemtype');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'tittle' => 'Item Type',
            'itemtype'=>TypeItem::find($id),
        ];

        return view('pages.master.itemtype.edit', $data);
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'name'=>'required',
        ]);

        $typeItem = TypeItem::find($id);

        $typeItem->update([
            'name'=>$request->name
        ]);

        return redirect()->route('itemtype');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = TypeItem::findOrFail($id);
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
