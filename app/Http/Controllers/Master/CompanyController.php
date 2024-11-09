<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'tittle' => 'Company'
        ];

        return view('pages.master.company.index', $data);
    }

    public function getData(Request $request)
    {
        $datatype = Company::orderByDesc('created_at')->get();

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
            'tittle' => 'Company'
        ];

        return view('pages.master.company.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Company $company)
    {
        $request->validate([
            'name'=>'required',
            'address'=>'required',
            'phone'=>'required',
        ]);

        $company->insert([
            'name'=>$request->name,
            'address'=>$request->address,
            'phone'=>$request->phone,
        ]);

        return redirect()->route('company');  
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
