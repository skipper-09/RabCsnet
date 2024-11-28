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
use App\Models\Summary;
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
        $currentUserRole = $currentUser->roles->first()->name;

        // For Developers, show ALL project reviews
        if ($currentUserRole === 'Developer') {
            $dataReview = ProjectReview::with(['project', 'reviewer'])
                ->orderByDesc('created_at')
                ->get();
        } else {
            // Existing logic for other roles
            $dataReview = ProjectReview::with(['project', 'reviewer'])
                ->orderByDesc('created_at')
                ->where('reviewer_id', $currentUser->id)
                ->get();
        }

        return DataTables::of($dataReview)
            ->addIndexColumn()
            ->addColumn('project', function ($item) {
                return $item->project ? $item->project->name : '-';
            })
            ->addColumn('reviewer', function ($item) {
                return $item->reviewer ? $item->reviewer->name : '-';
            })
            ->addColumn('status_pengajuan', function ($item) {
                $status = $item->project->status_pengajuan;

                switch ($status) {
                    case 'pending':
                        return '<span class="badge bg-warning">Pending</span>';
                    case 'in_review':
                        return '<span class="badge bg-primary">In Review</span>';
                    case 'approved':
                        return '<span class="badge bg-success">Approved</span>';
                    case 'Rejected':
                        return '<span class="badge bg-danger">Rejected</span>';
                    default:
                        return '<span class="badge bg-secondary">Unknown</span>';
                }
            })
            ->editColumn('review_date', function ($data) {
                return Carbon::parse($data->created_at)->format('Y-m-d');
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
            ->rawColumns(['action', 'project', 'reviewer', 'status_pengajuan', 'review_date'])
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
                $projects = Project::where('status_pengajuan', 'pending')
                    ->whereDoesntHave('ProjectReview', function ($query) use ($currentUser) {
                        $query->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Accounting');
                        });
                    })
                    ->whereHas('Projectfile') // Pastikan project memiliki file
                    ->with([
                        'Projectfile',
                        'summary',
                    ]) // Eager load summary with aggregation
                    ->get()
                    ->map(function ($project) {
                        // Format the total_summary as a number (e.g., with 2 decimal places)
                        $project->formatted_total_summary = number_format($project->summary->first()->total_summary ?? 0, 2, ',', '.');
                        return $project;
                    });
                break;

            case 'Owner':
                // Untuk owner, ambil project yang sudah direview accounting tapi belum direview owner
                $projects = Project::where('status_pengajuan', 'in_review')
                    ->whereHas('ProjectReview.reviewer.roles', function ($query) {
                        $query->where('name', 'Accounting');
                    })
                    ->whereDoesntHave('ProjectReview.reviewer.roles', function ($query) {
                        $query->where('name', 'Owner');
                    })
                    ->whereHas('Projectfile') // Pastikan project memiliki file
                    ->with([
                        'Projectfile',
                        'summary',
                    ]) // Eager load summary with aggregation
                    ->get()
                    ->map(function ($project) {
                        // Format the total_summary as a number (e.g., with 2 decimal places)
                        $project->formatted_total_summary = number_format($project->summary->first()->total_summary ?? 0, 2, ',', '.');
                        return $project;
                    });
                break;

            case 'Developer':
                // Untuk Developer, ambil SEMUA project yang belum fully reviewed
                $projects = Project::whereIn('status_pengajuan', ['pending', 'in_review'])
                    ->whereHas('Projectfile') // Pastikan project memiliki file
                    ->with([
                        'Projectfile',
                        'summary',
                    ]) // Eager load summary with aggregation
                    ->get()
                    ->map(function ($project) {
                        // Format the total_summary as a number (e.g., with 2 decimal places)
                        $project->formatted_total_summary = number_format($project->summary->first()->total_summary ?? 0, 2, ',', '.');
                        return $project;
                    });
                break;

            default:
                // Jika bukan accounting, owner, atau developer, kembalikan view dengan pesan
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
            switch ($currentUserRole) {
                case 'Accounting':
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

                    // Set status ke in_review untuk review accounting
                    $project->status_pengajuan = 'in_review';
                    break;

                case 'Owner':
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

                    // Owner bisa merubah status ke rejected atau approved
                    $project->status_pengajuan = $request->input('status_pengajuan', 'in_review');
                    // Jika status pengajuan rejected, maka status adalah canceled
                    if ($project->status_pengajuan == 'rejected') {
                        $project->status = 'canceled';

                        // Hapus file project dan summary berdasarkan project id
                        ProjectFile::where('project_id', $project->id)->delete();
                        Summary::where('project_id', $project->id)->delete();
                    }
                    break;

                case 'Developer':
                    // Developer dapat melakukan review di berbagai tahap
                    // Cek apakah review sudah ada
                    $existingReviews = ProjectReview::where('project_id', $project->id)
                        ->where('reviewer_id', $currentUser->id)
                        ->first();

                    if ($existingReviews) {
                        throw ValidationException::withMessages([
                            'project_id' => 'Anda sudah pernah melakukan review untuk project ini.'
                        ]);
                    }

                    // Developer bisa merubah status ke in_review atau approved
                    $project->status_pengajuan = $request->input('status_pengajuan', 'in_review');
                    // Jika status pengajuan rejected, maka status adalah canceled
                    if ($project->status_pengajuan == 'rejected') {
                        $project->status = 'canceled';

                        // Hapus file project dan summary berdasarkan project id
                        ProjectFile::where('project_id', $project->id)->delete();
                        Summary::where('project_id', $project->id)->delete();
                    }
                    break;

                default:
                    throw ValidationException::withMessages([
                        'project_id' => 'Anda tidak memiliki izin untuk melakukan review.'
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

    public function show($id)
    {
        try {
            // Find the project review with related project and reviewer
            $projectReview = ProjectReview::with(['project', 'project.Projectfile', 'reviewer'])
                ->findOrFail($id);

            // Get current user and role
            $currentUser = Auth::user();
            $currentUserRole = $currentUser->roles->first()->name;

            // Check access based on user role
            $allowedRoles = ['Developer', 'Accounting', 'Owner'];
            if (!in_array($currentUserRole, $allowedRoles)) {
                return redirect()->back()->with([
                    'status' => 'Error',
                    'message' => 'Anda tidak memiliki izin untuk melihat detail review.'
                ]);
            }

            // For Developers, allow full access to all reviews
            $data = [
                'tittle' => 'Detail Project Review',
                'review' => $projectReview,
            ];

            return view('pages.review.show', $data);
        } catch (Exception $e) {
            return redirect()->back()->with([
                'status' => 'Error',
                'message' => 'Terjadi kesalahan saat mengambil detail review. Silakan coba lagi.'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'review_note' => 'nullable|string|max:255',
                'project_id' => 'required|exists:projects,id',
            ], [
                'review_note.max' => 'Catatan review tidak boleh lebih dari 255 karakter.',
                'project_id.required' => 'Project wajib diisi.',
                'project_id.exists' => 'Project tidak valid.',
            ]);

            // Begin transaction
            DB::beginTransaction();

            // Get current user
            $currentUser = Auth::user();
            $currentUserRole = $currentUser->roles->first()->name;

            // Find the project review
            $projectReview = ProjectReview::findOrFail($id);

            // Check if user has permission to update
            // Developers now have full access to update any review
            $allowedRoles = ['Developer', 'Accounting', 'Owner'];
            if (!in_array($currentUserRole, $allowedRoles)) {
                throw new Exception('Anda tidak memiliki izin untuk mengubah review.');
            }

            // Check if the review belongs to the current user or current user is a Developer
            if ($projectReview->reviewer_id !== $currentUser->id && $currentUserRole !== 'Developer') {
                throw new Exception('Anda hanya dapat mengubah review milik sendiri.');
            }

            // Update the project review
            $projectReview->review_note = $validated['review_note'];
            $projectReview->save();

            // Commit transaction
            DB::commit();

            return redirect()
                ->route('review')
                ->with([
                    'status' => 'Success',
                    'message' => 'Berhasil memperbarui review project!'
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
                    'message' => $e->getMessage()
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
