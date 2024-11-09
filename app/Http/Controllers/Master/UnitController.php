<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'tittle' => 'Unit'
        ];

        return view('pages.master.unit.index', $data);
    }

    public function getData(Request $request)
    {
        $dataType = Unit::orderByDesc('id')->get();

        return DataTables::of($dataType)->addIndexColumn()->addColumn('action', function ($data) {
            $button = '';
            $button .= ' <a href="' . route('unit.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            class="fas fa-pencil-alt"></i></a>';
            $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unit.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
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
            'tittle' => 'Unit',
        ];

        return view('pages.master.unit.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $unit->insert([
            'name' => $request->name,
        ]);

        return redirect()->route('unit');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'tittle' => 'Unit',
            'unit' => Unit::find($id)
        ];

        return view('pages.master.unit.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $unit = Unit::find($id);

        $unit->update([
            'name' => $request->name,
        ]);

        return redirect()->route('unit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = Unit::findOrFail($id);
            $data->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Unit Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Unit !',
                'trace' => $e->getTrace()
            ]);
        }
    }
}