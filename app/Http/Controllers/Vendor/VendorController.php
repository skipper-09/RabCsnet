<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $data = [
            'tittle' => 'Vendor'
        ];

        return view('pages.vendor.index', $data);
    }



    public function getData()
    {
        $dataType = Vendor::with(['user'])->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
                $button .= '<a href="' . route('vendor.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>';
                $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('vendor.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->editColumn('user', function ($data) {
                return $data->user->name;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'tittle' => 'Vendor',
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'developer');
            })->whereDoesntHave('vendor')->get(),
        ];

        return view('pages.vendor.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required',
            'user_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan. Silakan gunakan email lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'address.required' => 'Alamat wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'user_id.required' => 'User wajib diisi.',
            'user_id.exists' => 'User tidak valid.',
        ]);
        Vendor::create($request->all());
        return redirect()->route('vendor')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Vendor']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = [
            'tittle' => 'Vendor',
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'developer');
            })->get(),
            'vendor' => Vendor::findOrFail($id),
        ];

        return view('pages.vendor.edit', $data);
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
        try {
            $data = Vendor::findOrFail($id);
            $data->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Vendor Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Vendor !',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
