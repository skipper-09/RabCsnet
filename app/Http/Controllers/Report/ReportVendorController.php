<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReportVendor;
use App\Models\Project;
use App\Models\Vendor;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportVendorController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Report Vendor'
        ];

        return view('pages.report.index', $data);
    }

    public function getData(Request $request)
    {
        $dataType = ReportVendor::with(['project', 'vendor'])
            ->orderByDesc('created_at')
            ->get();

        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('project', function ($item) {
                return $item->project ? $item->project->name : '-';  // Ensure correct access to project
            })
            ->addColumn('vendor', function ($item) {
                return $item->vendor ? $item->vendor->name : '-';  // Ensure correct access to vendor
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-reportvendors')) {
                    $button .= '<a href="' . route('report.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }
                if ($userauth->can('delete-reportvendors')) {
                    $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('report.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                    <i class="fas fa-trash-alt"></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'project', 'vendor'])
            ->make(true);
    }

    public function create()
    {
        $data = [
            'tittle' => 'Report Vendor',
            'projects' => Project::all(),
            'vendors' => Vendor::all(),
        ];

        return view('pages.report.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Added max file size
        ], [
            'project_id.required' => 'Project wajib diisi.',
            'project_id.exists' => 'Project tidak valid.',
            'vendor_id.required' => 'Vendor wajib diisi.',
            'vendor_id.exists' => 'Vendor tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
            'image.required' => 'Gambar wajib diisi.',
            'image.mimes' => 'Format gambar tidak valid.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
        ]);

        $filename = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'reportvendor_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/images/reportvendor/'), $filename);
        }

        $reportVendor = ReportVendor::create([
            'project_id' => $request->project_id,
            'vendor_id' => $request->vendor_id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $filename,
        ]);

        return redirect()->route('report')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Report!']);
    }

    public function show($id)
    {
        $data = [
            'tittle' => 'Report Vendor',
            'report' => ReportVendor::find($id),
            'projects' => Project::all(),
            'vendors' => Vendor::all(),
        ];

        return view('pages.report.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Added max file size
        ], [
            'project_id.required' => 'Project wajib diisi.',
            'project_id.exists' => 'Project tidak valid.',
            'vendor_id.required' => 'Vendor wajib diisi.',
            'vendor_id.exists' => 'Vendor tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau svg.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
        ]);

        $reportVendor = ReportVendor::findOrFail($id); // Added error handling

        $filename = $reportVendor->image;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'reportvendor_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/images/reportvendor/'), $filename);
            if ($reportVendor->image !== 'default.png' && file_exists(public_path('storage/images/reportvendor/' . $reportVendor->image))) {
                File::delete(public_path('storage/images/reportvendor/' . $reportVendor->image));
            }
        }

        $reportVendor->update([
            'project_id' => $request->project_id,
            'vendor_id' => $request->vendor_id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $filename,
        ]);

        return redirect()->route('report')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Report!']);
    }

    public function destroy($id)
    {
        try {
            $reportVendor = ReportVendor::findOrFail($id);
            $reportVendorData = $reportVendor->toArray(); // Capture the data before deletion

            $reportVendor->delete();

            // Log activity for d$data deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($reportVendor) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $reportVendorData // The data before deletion
                ])
                ->log('Item dihapus dengan nama ' . $reportVendor->name);

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
