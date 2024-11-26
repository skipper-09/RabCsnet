<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Exception;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class ProjectReviewController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Project Review'
        ];

        return view('pages.review.index', $data);
    }

    public function getData(Request $request)
    {
        $currentUser = Auth::user();

        $dataReview = ProjectReview::with(['project', 'reviewer'])
            ->orderByDesc('created_at')
            ->where('reviewer_id', $currentUser->id)
            ->get();

        return DataTables::of($dataReview)
            ->addIndexColumn()
            ->addColumn('project', function ($item) {
                return $item->project ? $item->project->name : '-';  // Ensure correct access to project
            })
            ->addColumn('reviewer', function ($item) {
                return $item->reviewer ? $item->reviewer->name : '-';  // Ensure correct access to reviewer
            })
            ->editColumn('review_date', function ($data) {
                // Menampilkan waktu aktivitas yang terformat
                return Carbon::parse($data->payment_date)->format('Y-m-d');
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-projectreviews')) {
                    $button .= '<a href="' . route('review.edit', $data->id) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }
                if ($userauth->can('delete-projectreviews')) {
                    $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('review.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                    <i class="fas fa-trash-alt"></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'project', 'reviewer', 'review_date'])
            ->make(true);
    }

    public function create()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Dapatkan user yang sedang login
        $currentUser = Auth::user();

        // Pastikan user memiliki role
        if (!$currentUser->roles->first()) {
            return redirect()->back()->with('error', 'User tidak memiliki role yang valid.');
        }

        // Dapatkan role user saat ini
        $currentUserRole = $currentUser->roles->first()->name;

        // Query untuk mengambil project berdasarkan review
        $projects = collect();

        switch ($currentUserRole) {
            case 'Accounting':
                // Untuk accounting, ambil project yang belum direview accounting
                $projects = Project::where('status', 'pending')
                    ->whereDoesntHave('ProjectReview', function ($query) use ($currentUser) {
                        $query->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Accounting');
                        });
                    })
                    ->whereHas('Projectfile') // Pastikan project memiliki file
                    ->with(['Projectfile', 'ProjectReview']) // Eager load relasi
                    ->get();
                break;

            case 'Owner':
                // Untuk owner, ambil project yang sudah direview accounting tapi belum direview owner
                $projects = Project::where('status', 'pending')
                    ->whereHas('ProjectReview.reviewer.roles', function ($query) {
                        $query->where('name', 'Accounting');
                    })
                    ->whereDoesntHave('ProjectReview.reviewer.roles', function ($query) {
                        $query->where('name', 'Owner');
                    })
                    ->whereHas('Projectfile') // Pastikan project memiliki file
                    ->with(['Projectfile', 'ProjectReview']) // Eager load relasi
                    ->get();
                break;

            default:
                // Jika bukan accounting atau owner, kembalikan view dengan pesan
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk me-review project.');
        }

        $data = [
            'tittle' => 'Project Review',
            'projects' => $projects
        ];

        return view('pages.review.add', $data);
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'review_note' => 'nullable|string|max:255',
            ], [
                'project_id.required' => 'Project wajib diisi.',
                'project_id.exists' => 'Project tidak valid.',
                'review_note.max' => 'Catatan review tidak boleh lebih dari 255 karakter.',
            ]);

            // Begin transaction
            DB::beginTransaction();

            // Get current user
            $currentUser = Auth::user();

            // Dapatkan role user saat ini
            $currentUserRole = $currentUser->roles->first()->name;

            // Check if project exists and can be reviewed
            $project = Project::findOrFail($validated['project_id']);

            // Tambahkan validasi project file sebelum review
            $projectFile = ProjectFile::where('project_id', $project->id)->first();
            if (!$projectFile) {
                throw ValidationException::withMessages([
                    'project_id' => 'Project file belum diisi. Harap lengkapi file project terlebih dahulu.'
                ]);
            }

            // Logika review berdasarkan role
            if ($currentUserRole === 'Accounting') {
                // Cek apakah project sudah direview oleh accounting
                $existingAccountingReview = ProjectReview::where('project_id', $project->id)
                    ->whereHas('reviewer.roles', function ($query) {
                        $query->where('name', 'Accounting');
                    })
                    ->first();

                if ($existingAccountingReview) {
                    throw ValidationException::withMessages([
                        'project_id' => 'Project ini sudah direview oleh user accounting.'
                    ]);
                }

                // Set status ke pending untuk review accounting
                $project->status = 'pending';
            } elseif ($currentUserRole === 'Owner') {
                // Cek apakah sudah ada review owner sebelumnya
                $existingOwnerReview = ProjectReview::where('project_id', $project->id)
                    ->whereHas('reviewer.roles', function ($query) {
                        $query->where('name', 'Owner');
                    })
                    ->first();

                if ($existingOwnerReview) {
                    throw ValidationException::withMessages([
                        'project_id' => 'Project ini sudah direview oleh owner.'
                    ]);
                }

                // Set status ke approved untuk review owner
                $project->status = 'approved';
            } else {
                throw ValidationException::withMessages([
                    'project_id' => 'Anda tidak memiliki izin untuk melakukan review.'
                ]);
            }

            // Cek apakah user saat ini sudah pernah review project ini
            $userExistingReview = ProjectReview::where('project_id', $project->id)
                ->where('reviewer_id', $currentUser->id)
                ->first();

            if ($userExistingReview) {
                throw ValidationException::withMessages([
                    'project_id' => 'Anda sudah pernah melakukan review untuk project ini.'
                ]);
            }

            // Create project review
            $projectReview = ProjectReview::create([
                'project_id' => $validated['project_id'],
                'reviewer_id' => $currentUser->id,
                'review_note' => $validated['review_note'],
                'review_date' => now(),
            ]);

            // Simpan perubahan status project
            $project->save();

            // Commit transaction
            DB::commit();

            return redirect()
                ->route('review')
                ->with([
                    'status' => 'Success',
                    'message' => 'Berhasil menambahkan review project!'
                ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with([
                    'status' => 'Error',
                    'message' => 'Terjadi kesalahan saat menambahkan review project. Silakan coba lagi.'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $projectReview = ProjectReview::findOrFail($id);
            $projectReviewData = $projectReview->toArray(); // Capture the data before deletion

            $projectReview->delete();

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
