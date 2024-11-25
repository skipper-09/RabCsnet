<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Exception;
use App\Models\User;
use App\Models\Project;
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
        $data = [
            'tittle' => 'Project Review',
            // get all project that have status "pending"
            'projects' => Project::where('status', 'pending')->get()
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

            // Check if project exists and can be reviewed
            $project = Project::findOrFail($validated['project_id']);

            // Check if project has already been reviewed
            $existingReview = ProjectReview::where('project_id', $project->id)
                ->where('reviewer_id', $currentUser->id)
                ->whereDate('review_date', Carbon::parse(now()))
                ->first();

            if ($existingReview) {
                throw ValidationException::withMessages([
                    'project_id' => 'Project ini sudah direview untuk tanggal yang sama.'
                ]);
            }

            // Create project review
            $projectReview = ProjectReview::create([
                'project_id' => $validated['project_id'],
                'reviewer_id' => $currentUser->id,
                'review_note' => $validated['review_note'],
                'review_date' => now(),
            ]);

            // Update project status
            $project->status = 'approved';
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
            redirect()
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
