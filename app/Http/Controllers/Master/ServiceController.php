<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'tittle' => 'Service'
        ];

        return view('pages.master.service.index', $data);
    }

    public function getData()
    {
        $dataType = Service::orderByDesc('created_at')
            ->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->editColumn('price', function ($data) {
                return $data->price ? 'Rp ' . number_format($data->price, 2, ',', '.') : '-';
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-services')) {
                    $button .= '<a href="' . route('service.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }
                if ($userauth->can('delete-services')) {
                    $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('service.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                    <i class="fas fa-trash-alt"></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'price'])
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'tittle' => 'Service',
        ];

        return view('pages.master.service.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'price.required' => 'Harga material wajib diisi.',
            'price.numeric' => 'Harga material harus berupa angka.',
        ]);

        // Simpan data service
        $service = Service::create($request->all());

        // Log activity for company creation
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($service) // The entity being changed
            ->event('created') // Event of the action
            ->withProperties([
                'attributes' => $service->toArray() // The data that was created
            ])
            ->log('Service dibuat dengan nama ' . $service->name);

        return redirect()->route('service')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Jasa']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'tittle' => 'Service',
            'service' => Service::find($id),
        ];

        return view('pages.master.service.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'price.required' => 'Harga material wajib diisi.',
            'price.numeric' => 'Harga material harus berupa angka.',
        ]);

        // Simpan data service
        $service = Service::find($id);
        $oldService = $service->toArray();

        $service->update($request->all());

        // Log activity for company update
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($service) // The entity being changed
            ->event('updated') // Event of the action
            ->withProperties([
                'old' => $oldService, // The data before update
                'attributes' => $service->toArray() // The updated data
            ])
            ->log('Service di update dengan nama ' . $service->name);

        return redirect()->route('service')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Jasa']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $serviceData = $service->toArray(); // Capture the data before deletion

            $service->delete();

            // Log activity for d$data deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($service) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $serviceData // The data before deletion
                ])
                ->log('Service dihapus dengan nama ' . $service->name);

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
