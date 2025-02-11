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
        $currentUserRole = $currentUser->roles->first()?->name;

        $query = ProjectReview::with(['project', 'reviewer'])
            ->orderByDesc('created_at');

        // Filter by reviewer_id for specific roles
        if (in_array($currentUserRole, ['Accounting', 'Owner'])) {
            $query->where('reviewer_id', $currentUser->id);
        }

        $dataReview = $query->get();

        return DataTables::of($dataReview)
            ->addIndexColumn()
            ->addColumn('project', function ($item) {
                return $item->project?->name ?? '-';
            })
            ->addColumn('reviewer', function ($item) {
                return $item->reviewer?->name ?? '-';
            })
            ->editColumn('status_review', function ($item) {
                $statusMap = [
                    'pending' => [
                        'class' => 'primary',
                        'text' => 'Pending'
                    ],
                    'in_review' => [
                        'class' => 'info',
                        'text' => 'In Review'
                    ],
                    'approved' => [
                        'class' => 'success',
                        'text' => 'Approved'
                    ],
                    'revision' => [
                        'class' => 'warning',
                        'text' => 'Revision'
                    ],
                    'rejected' => [
                        'class' => 'danger',
                        'text' => 'Rejected'
                    ]
                ];

                if (!$item->status_review || !isset($statusMap[$item->status_review])) {
                    return '<span class="badge badge-pill badge-soft-secondary font-size-13">No Review</span>';
                }

                $status = $statusMap[$item->status_review];
                return sprintf(
                    '<span class="badge badge-pill badge-soft-%s font-size-13">%s</span>',
                    $status['class'],
                    $status['text']
                );
            })
            ->editColumn('review_date', function ($data) {
                return Carbon::parse($data->created_at)->format('d-M-Y H:i:s');
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $buttons = [];

                if ($userauth->can('update-projectreviews')) {
                    $buttons[] = sprintf(
                        '<a href="%s" class="btn btn-sm btn-success action mr-1" data-id="%d" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                        <i class="fas fa-pencil-alt"></i>
                    </a>',
                        route('review.edit', $data->id),
                        $data->id
                    );
                }

                if ($userauth->can('delete-projectreviews')) {
                    $buttons[] = sprintf(
                        '<button class="btn btn-sm btn-danger action" data-id="%d" data-type="delete" data-route="%s" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                        <i class="fas fa-trash-alt"></i>
                    </button>',
                        $data->id,
                        route('review.delete', $data->id)
                    );
                }

                return '<div class="d-flex gap-2">' . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['action', 'project', 'reviewer', 'status_review', 'review_date'])
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
                $projects = $this->getAccountingProjects();
                break;

            case 'Owner':
                $projects = $this->getOwnerProjects();
                break;

            case 'Developer':
                $projects = $this->getDeveloperProjects();
                break;

            default:
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk me-review project.');
        }

        $data = [
            'tittle' => 'Project Review',
            'projects' => $projects
        ];

        return view('pages.review.add', $data);
    }

    /**
     * Mengambil proyek untuk role Accounting.
     */
    private function getAccountingProjects()
    {
        return Project::where(function ($query) {
            // Cek proyek yang belum memiliki review sama sekali
            $query->whereDoesntHave('ProjectReview')
                // ATAU proyek yang memiliki review dengan status pending/in_review
                ->orWhereHas('ProjectReview', function ($q) {
                    $q->whereIn('status', ['pending', 'in_review']);
                })
                // Pastikan proyek tidak memiliki review dengan status_review tertentu
                ->whereDoesntHave('ProjectReview', function ($q) {
                    $q->where('status_review', 'approved'); // Ganti 'status_to_exclude' dengan nilai yang sesuai
                });
        })
            // Pastikan proyek memiliki file terkait
            ->whereHas('Projectfile')
            // Load relasi yang dibutuhkan
            ->with([
                'Projectfile',
                'summary',
                'ProjectReview' => function ($query) {
                    $query->latest(); // Ambil review terbaru
                },
                'ProjectReview.reviewer'
            ])
            ->get()
            ->map(function ($project) {
                return $this->formatProjectData($project);
            });
    }

    /**
     * Mengambil proyek untuk role Owner.
     */
    private function getOwnerProjects()
    {
        return Project::whereHas('Projectfile')
            ->whereDoesntHave('ProjectReview', function ($q) {
                $q->where('status_review', 'approved'); // Ganti dengan status_review yang ingin dikecualikan
            })
            ->with(['Projectfile', 'summary', 'ProjectReview.reviewer'])
            ->get()
            ->map(function ($project) {
                return $this->formatProjectData($project);
            });
    }

    /**
     * Mengambil proyek untuk role Developer.
     */
    private function getDeveloperProjects()
    {
        return Project::whereHas('Projectfile')
            ->whereDoesntHave('ProjectReview', function ($q) {
                $q->where('status_review', 'approved'); // Ganti dengan status_review yang ingin dikecualikan
            })
            ->with(['Projectfile', 'summary', 'ProjectReview.reviewer'])
            ->get()
            ->map(function ($project) {
                return $this->formatProjectData($project);
            });
    }

    /**
     * Memformat data proyek.
     */
    private function formatProjectData($project)
    {
        // Format total summary
        $project->formatted_total_summary = number_format(
            $project->summary->total_summary ?? 0,
            2,
            ',',
            '.'
        );

        // Periksa apakah ada review yang terkait dengan proyek
        $lastReview = $project->ProjectReview->last();

        if ($lastReview) {
            // Jika ada review, ambil reviewer dan review note
            $project->reviewed_by = $lastReview->reviewer->name ?? 'Tidak ada reviewer';
            $project->review_note = $lastReview->review_note ?? 'Tidak ada catatan';
        } else {
            // Jika tidak ada review, set default
            $project->reviewed_by = 'Belum Direview';
            $project->review_note = 'Tidak ada catatan';
        }

        return $project;
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'review_note' => 'nullable|string|max:255',
                'status_review' => 'required|in:pending,in_review,approved,rejected,revision',
            ], [
                'project_id.required' => 'Project wajib diisi.',
                'project_id.exists' => 'Project tidak valid.',
                'review_note.max' => 'Catatan review tidak boleh lebih dari 255 karakter.',
                'status_review.required' => 'Status review wajib diisi.',
                'status_review.in' => 'Status review tidak valid.',
            ]);

            // Begin transaction
            DB::beginTransaction();

            $currentUser = Auth::user();
            $currentUserRole = $currentUser->roles->first()?->name;

            if (!$currentUserRole) {
                throw ValidationException::withMessages([
                    'role' => 'User tidak memiliki role yang valid.'
                ]);
            }

            // Get project with its relationships
            $project = Project::with(['Projectfile', 'ProjectReview'])->findOrFail($validated['project_id']);

            // Validate project file existence
            if (!$project->Projectfile) {
                throw ValidationException::withMessages([
                    'project_id' => 'Project file belum diisi. Harap lengkapi file project terlebih dahulu.'
                ]);
            }

            // Define allowed status transitions per role
            $allowedStatusTransitions = [
                'Accounting' => ['pending', 'in_review', 'revision'],
                'Owner' => ['pending', 'in_review', 'approved', 'rejected', 'revision'],
                'Developer' => ['pending', 'in_review', 'approved', 'rejected', 'revision'],
            ];

            // Validate user's role and status transition
            if (!isset($allowedStatusTransitions[$currentUserRole])) {
                throw ValidationException::withMessages([
                    'role' => 'Anda tidak memiliki izin untuk melakukan review.'
                ]);
            }

            $requestedStatus = $validated['status_review'];
            if (!in_array($requestedStatus, $allowedStatusTransitions[$currentUserRole])) {
                throw ValidationException::withMessages([
                    'status_review' => "Role {$currentUserRole} tidak dapat mengubah status ke {$requestedStatus}."
                ]);
            }

            // Create project review
            $projectReview = new ProjectReview([
                'project_id' => $validated['project_id'],
                'reviewer_id' => $currentUser->id,
                'review_note' => $validated['review_note'],
                'review_date' => now(),
                'status_review' => $requestedStatus
            ]);

            $projectReview->save();

            // Update project status based on review status and role
            $this->updateProjectStatus($project, $requestedStatus, $currentUserRole);

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

    /**
     * Update project status based on review status and user role
     * 
     * @param Project $project
     * @param string $reviewStatus
     * @param string $userRole
     * @return void
     */
    private function updateProjectStatus(Project $project, string $reviewStatus, string $userRole)
    {
        switch ($reviewStatus) {
            case 'rejected':
                $this->handleRejectedStatus($project);
                break;

            case 'revision':
                $this->handleRevisionStatus($project, $userRole);
                break;

            case 'approved':
                if (in_array($userRole, ['Owner', 'Developer'])) {
                    $project->status = 'in_progres';
                }
                break;

            case 'in_review':
                // No changes to project status for in_review
                break;

            case 'pending':
                // No changes to project status for pending
                break;
        }

        $project->save();
    }

    /**
     * Handle rejected status for a project
     * 
     * @param Project $project
     * @return void
     */
    private function handleRejectedStatus(Project $project)
    {
        $project->status = 'canceled';
        $project->start_status = false;

        // Delete related records
        if ($project->Projectfile) {
            $project->Projectfile->delete();
        }

        if ($project->summary) {
            $project->summary->delete();
        }
    }

    /**
     * Handle revision status for a project
     * 
     * @param Project $project
     * @param string $userRole
     * @return void
     */
    private function handleRevisionStatus(Project $project, string $userRole)
    {
        if (in_array($userRole, ['Owner', 'Developer', 'Accounting'])) {
            $project->status = 'pending';
            $project->start_status = false;

            // Delete related records
            if ($project->Projectfile) {
                $project->Projectfile->delete();
            }

            if ($project->summary) {
                $project->summary->delete();
            }
        }
        // For Accounting role, revision doesn't change project status or delete files
    }

    public function show($id)
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

        try {
            // Fetch project review dengan relasi yang dibutuhkan
            $projectReview = ProjectReview::with([
                'project.Projectfile',
                'project.summary',
                'project.ProjectReview.reviewer',
                'reviewer'
            ])->findOrFail($id);

            // Format data project
            $project = $this->formatProjectData($projectReview->project);

            // Validasi akses berdasarkan role
            switch ($currentUserRole) {
                case 'Accounting':
                case 'Owner':
                case 'Developer':
                    $data = [
                        'tittle' => 'Edit Project Review',
                        'project' => $project,
                        'review' => $projectReview
                    ];
                    return view('pages.review.edit', $data);

                default:
                    return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat review project.');
            }

        } catch (Exception $e) {
            return redirect()->back()->with([
                'status' => 'Error',
                'message' => 'Terjadi kesalahan saat mengambil detail review. Silakan coba lagi.'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'review_note' => 'nullable|string|max:255',
        ], [
            'review_note.max' => 'Catatan review tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            // Begin transaction
            DB::beginTransaction();

            $currentUser = Auth::user();
            $currentUserRole = $currentUser->roles->first()?->name;

            if (!$currentUserRole) {
                throw ValidationException::withMessages([
                    'role' => 'User tidak memiliki role yang valid.'
                ]);
            }

            // Get project review
            $projectReview = ProjectReview::findOrFail($id);

            // Update review note
            $projectReview->review_note = $validated['review_note'];
            $projectReview->save();

            DB::commit();

            return redirect()
                ->route('review')
                ->with([
                    'status' => 'Success',
                    'message' => 'Berhasil memperbarui catatan review!'
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
                    'message' => 'Terjadi kesalahan saat memperbarui catatan review. Silakan coba lagi.'
                ]);
        }
    }

    private function validateUserAccess()
    {
        if (!Auth::check()) {
            throw new Exception('Anda harus login terlebih dahulu.');
        }

        $currentUser = Auth::user();
        if (!$currentUser->roles->first()) {
            throw new Exception('User tidak memiliki role yang valid.');
        }

        return $currentUser;
    }

    private function hasViewPermission(string $role): bool
    {
        return in_array($role, ['Developer', 'Accounting', 'Owner']);
    }

    private function canEditReview(string $role): bool
    {
        return in_array($role, ['Developer', 'Accounting', 'Owner']);
    }

    private function prepareProjectData($project)
    {
        // Format total summary
        $project->formatted_total_summary = number_format(
            $project->summary->first()->total_summary ?? 0,
            2,
            ',',
            '.'
        );

        // Add review information
        $project->reviewed_by = $project->ProjectReview->isEmpty()
            ? 'Belum Direview'
            : $project->ProjectReview->last()->reviewer->name;

        $project->review_note = $project->ProjectReview->isEmpty()
            ? 'Tidak ada catatan'
            : $project->ProjectReview->last()->review_note;

        return $project;
    }

    private function validateUpdateRequest(Request $request)
    {
        return $request->validate([
            'review_note' => 'nullable|string|max:255',
            'status_review' => 'nullable|in:pending,in_review,approved,rejected',
        ], [
            'review_note.max' => 'Catatan review tidak boleh lebih dari 255 karakter.',
            'status_review.in' => 'Status pengajuan tidak valid.'
        ]);
    }

    private function validateUserRole($user)
    {
        $role = $user->roles->first()?->name;
        if (!$role) {
            throw new Exception('User tidak memiliki role yang valid.');
        }
        return $role;
    }

    private function hasUpdatePermission(string $role): bool
    {
        return in_array($role, ['Developer', 'Accounting', 'Owner']);
    }

    private function processReviewUpdate($projectReview, $project, array $validated, string $role)
    {
        // Update review note if provided
        if (isset($validated['review_note'])) {
            $projectReview->review_note = $validated['review_note'];
        }

        // Process status update based on role
        if (isset($validated['status_review'])) {
            $this->updateReviewStatus($project, $projectReview, $validated['status_review'], $role);
        }

        $projectReview->save();
    }

    private function updateReviewStatus($project, $projectReview, string $status, string $role)
    {
        $statusHandlers = [
            'Accounting' => function () use ($status, $project) {
                if ($status !== 'in_review') {
                    throw new Exception('Accounting hanya dapat mengubah status menjadi in_review.');
                }
                $project->ProjectReview->status_review = 'in_review';
            },
            'Owner' => function () use ($status, $project) {
                if (!in_array($status, ['approved', 'rejected'])) {
                    throw new Exception('Owner hanya dapat mengubah status menjadi approved atau rejected.');
                }
                $this->handleOwnerStatusUpdate($project, $status);
            },
            'Developer' => function () use ($status, $project) {
                $project->ProjectReview->status_review = $status;
                if (in_array($status, ['rejected', 'revision'])) {
                    $this->cancelProject($project);
                }
            }
        ];

        if (isset($statusHandlers[$role])) {
            $statusHandlers[$role]();
            $project->save();
        }
    }

    private function handleOwnerStatusUpdate($project, string $status)
    {
        $project->ProjectReview->status_review = $status;

        if ($status === 'rejected') {
            $this->cancelProject($project);
        }
    }

    private function cancelProject($project)
    {
        $project->status = 'canceled';
        $project->start_status = false;

        // Delete related records
        ProjectFile::where('project_id', $project->id)->delete();
        Summary::where('project_id', $project->id)->delete();
    }

    private function handleException(Exception $e)
    {
        return redirect()->back()->with([
            'status' => 'Error',
            'message' => 'Terjadi kesalahan saat mengambil detail review. Silakan coba lagi.'
        ]);
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
