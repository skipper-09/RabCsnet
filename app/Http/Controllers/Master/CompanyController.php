<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Exception;
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
        $company = Company::orderByDesc('id')->get();

        return DataTables::of($company)->addIndexColumn()->addColumn('action', function ($data) {
            $button = '';
            $button .= ' <a href="' . route('company.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            class="fas fa-pencil-alt"></i></a>';
            $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('company.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
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
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required|numeric',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
        ]);

        $company->create($request->all());

        return redirect()->route('company')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Company']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'tittle' => 'Company',
            'company' => Company::find($id)
        ];

        return view('pages.master.company.edit', $data);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'name' => 'required',
                'address' => 'required',
                'phone' => 'required|numeric',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'address.required' => 'Alamat wajib diisi.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.numeric' => 'Nomor telepon harus berupa angka.',
            ]);

            $company = Company::find($id);

            $company->update($request->all());

            return redirect()->route('company')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Company']);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = Company::findOrFail($id);
            $data->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Company Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Company!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
