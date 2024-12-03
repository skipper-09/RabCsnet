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
        $user = Auth::user();
        $userRole = $user->roles->first() ? $user->roles->first()->name : null;

        if (!$userRole) {
            return redirect()->route('dashboard')->withErrors('User does not have an assigned role.');
        }

        if ($userRole !== 'Vendor') {
            // Non-vendor users, fetch all vendors and active projects
            $dataType = ReportVendor::with(['project', 'vendor'])
                ->orderByDesc('created_at')
                ->get();
        } else {
            // Vendor users, filter projects by the vendor's id
            $vendor = Vendor::where('user_id', $user->id)->first();

            if (!$vendor) {
                return redirect()->route('dashboard')->withErrors('No vendor found for the user.');
            }

            $dataType = ReportVendor::with(['project', 'vendor'])
                ->where('vendor_id', $vendor->id)  // Filter projects by vendor
                ->orderByDesc('created_at')
                ->get();
        }

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
        $user = Auth::user();
        $userRole = $user->roles->first() ? $user->roles->first()->name : null;

        if (!$userRole) {
            return redirect()->route('dashboard')->withErrors('User does not have an assigned role.');
        }

        if ($userRole !== 'Vendor') {
            // Non-vendor users, fetch all vendors and active projects
            $data = [
                'tittle' => 'Report Vendor',
                'projects' => Project::where('start_status', 1)->get(),
                'vendors' => Vendor::all(),
                'userRole' => $userRole,  // Pass user role to the view
            ];
        } else {
            // Vendor users, filter projects by the vendor's id
            $vendor = Vendor::where('user_id', $user->id)->first();

            if (!$vendor) {
                return redirect()->route('dashboard')->withErrors('No vendor found for the user.');
            }

            $data = [
                'tittle' => 'Report Vendor',
                'projects' => Project::where('start_status', 1)
                    ->where('vendor_id', $vendor->id)  // Filter projects by vendor
                    ->get(),
                'vendors' => $vendor,  // Single vendor associated with the user
                'userRole' => $userRole,  // Pass user role to the view
            ];
        }

        return view('pages.report.add', $data);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $userRole = $user->roles->first() ? $user->roles->first()->name : null;

            if (!$userRole) {
                return redirect()->route('dashboard')->withErrors('User does not have an assigned role.');
            }

            // Dynamic validation rules based on user role
            $validationRules = [
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            ];

            // If the user is not a Vendor, validate 'vendor_id'
            if ($userRole !== 'Vendor') {
                $validationRules['vendor_id'] = 'required|exists:vendors,id';
            }

            $request->validate($validationRules, [
                'project_id.required' => 'Project wajib diisi.',
                'project_id.exists' => 'Project tidak valid.',
                'vendor_id.required' => 'Vendor wajib diisi.',
                'vendor_id.exists' => 'Vendor tidak valid.',
                'title.required' => 'Judul wajib diisi.',
                'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
                'image.required' => 'Gambar wajib diisi.',
                'image.mimes' => 'Format gambar tidak valid.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            ]);

            // Handle image upload
            $filename = '';
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'reportvendor_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/reportvendor/'), $filename);
            }

            // Create the report based on user role
            if ($userRole !== 'Vendor') {
                // For non-Vendor users, 'vendor_id' should be passed in the request
                $reportVendor = ReportVendor::create([
                    'project_id' => $request->project_id,
                    'vendor_id' => $request->vendor_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $filename,
                ]);
            } else {
                // For Vendor users, use the logged-in user's vendor ID
                $vendor = Vendor::where('user_id', $user->id)->first();

                if (!$vendor) {
                    return redirect()->back()->withErrors('No vendor found for the logged-in user.');
                }

                $reportVendor = ReportVendor::create([
                    'project_id' => $request->project_id,
                    'vendor_id' => $vendor->id,  // Use the vendor ID associated with the user
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $filename,
                ]);
            }

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($reportVendor)
                ->event('created')
                ->log('Report Vendor dibuat dengan judul ' . $reportVendor->title);

            return redirect()->route('report')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Report!']);
        } catch (Exception $e) {
            \Log::error('Error occurred while creating report vendor', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $userRole = $user->roles->first() ? $user->roles->first()->name : null;

        // Pastikan role valid dan sesuai
        if (!$userRole) {
            return redirect()->route('dashboard')->withErrors('User does not have an assigned role.');
        }

        // Temukan laporan vendor yang akan diedit
        $reportVendor = ReportVendor::find($id);
        if (!$reportVendor) {
            return redirect()->route('report')->withErrors('Report not found.');
        }

        // Tentukan data yang dikirim berdasarkan role pengguna
        if ($userRole !== 'Vendor') {
            // Untuk pengguna selain Vendor, dapatkan semua vendor dan proyek aktif
            $data = [
                'tittle' => 'Edit Report Vendor',
                'report' => $reportVendor,
                'projects' => Project::all(),
                'vendors' => Vendor::all(),
                'userRole' => $userRole, // Kirim role pengguna ke view
            ];
        } else {
            // Untuk pengguna Vendor, hanya tampilkan proyek yang terkait dengan vendor
            $vendor = Vendor::where('user_id', $user->id)->first();

            if (!$vendor) {
                return redirect()->route('dashboard')->withErrors('No vendor found for the user.');
            }

            $data = [
                'tittle' => 'Edit Report Vendor',
                'report' => $reportVendor,
                'projects' => Project::where('vendor_id', $vendor->id)->get(),
                'vendors' => $vendor,
                'userRole' => $userRole,  // Kirim role pengguna ke view
            ];
        }

        return view('pages.report.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $userRole = $user->roles->first() ? $user->roles->first()->name : null;

            // Pastikan role valid dan sesuai
            if (!$userRole) {
                return redirect()->route('dashboard')->withErrors('User does not have an assigned role.');
            }

            // Validasi data yang diterima
            $validationRules = [
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5048', // Gambar opsional pada update
            ];

            // Jika pengguna bukan Vendor, maka validasi vendor_id
            if ($userRole !== 'Vendor') {
                $validationRules['vendor_id'] = 'required|exists:vendors,id';
            }

            // Validasi berdasarkan rules yang sudah disesuaikan
            $request->validate($validationRules, [
                'project_id.required' => 'Project wajib diisi.',
                'project_id.exists' => 'Project tidak valid.',
                'vendor_id.required' => 'Vendor wajib diisi.',
                'vendor_id.exists' => 'Vendor tidak valid.',
                'title.required' => 'Judul wajib diisi.',
                'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Format gambar tidak valid.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            ]);

            // Temukan reportVendor berdasarkan id
            $reportVendor = ReportVendor::findOrFail($id); // Pastikan laporan ditemukan

            // Tentukan file gambar yang baru jika ada
            $filename = $reportVendor->image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'reportvendor_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/reportvendor/'), $filename);

                // Hapus gambar lama jika ada
                if ($reportVendor->image !== 'default.png' && file_exists(public_path('storage/images/reportvendor/' . $reportVendor->image))) {
                    File::delete(public_path('storage/images/reportvendor/' . $reportVendor->image));
                }
            }

            // Jika pengguna adalah Vendor, dapatkan ID vendor mereka dan tidak perlu vendor_id dari request
            if ($userRole === 'Vendor') {
                $vendor = Vendor::where('user_id', $user->id)->first();

                if (!$vendor) {
                    return redirect()->route('dashboard')->withErrors('No vendor found for the logged-in user.');
                }

                // Update laporan menggunakan vendor_id yang terkait dengan pengguna
                $reportVendor->update([
                    'project_id' => $request->project_id,
                    'vendor_id' => $vendor->id,  // Gunakan ID vendor yang terkait dengan pengguna
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $filename,
                ]);
            } else {
                // Jika bukan Vendor, vendor_id akan berasal dari request
                $reportVendor->update([
                    'project_id' => $request->project_id,
                    'vendor_id' => $request->vendor_id,  // Gunakan vendor_id yang diberikan oleh pengguna
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $filename,
                ]);
            }

            // Log aktivitas pembaruan
            activity()
                ->causedBy(Auth::user())
                ->performedOn($reportVendor)
                ->event('updated')
                ->log('Report Vendor diubah dengan judul ' . $reportVendor->title);

            return redirect()->route('report')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Report!']);
        } catch (Exception $e) {
            // Log exception message jika terjadi error
            \Log::error('Error occurred while updating report vendor', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            // Return error message jika gagal
            return redirect()->back()
                ->with('error', 'Failed to update data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $userRole = $user->roles->first() ? $user->roles->first()->name : null;

            if (!$userRole) {
                return redirect()->route('dashboard')->withErrors('User does not have an assigned role.');
            }

            if ($userRole !== 'Vendor') {
                $reportVendor = ReportVendor::findOrFail($id);
                $reportVendorData = $reportVendor->toArray(); // Capture the data before deletion

                $reportVendor->delete();
            } else {
                $vendor = Vendor::where('user_id', $user->id)->first();

                if (!$vendor) {
                    return redirect()->route('dashboard')->withErrors('No vendor found for the logged-in user.');
                }

                $reportVendor = ReportVendor::where('vendor_id', $vendor->id)->findOrFail($id);
                $reportVendorData = $reportVendor->toArray(); // Capture the data before deletion

                $reportVendor->delete();
            }

            // Log activity for d$data deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($reportVendor) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $reportVendorData // The data before deletion
                ])
                ->log('Report Vendor dihapus dengan judul ' . $reportVendor->title);

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
