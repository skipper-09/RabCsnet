<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class TaskAssignController extends Controller
{
    /**
     * Display task assignments index page
     */
    public function index()
    {
        $data = [
            'tittle' => 'Task Assignments'
        ];

        return view('pages.tasks.assign.index', $data);
    }

    /**
     * Fetch task assignment data for DataTables
     */
    public function getData()
    {
        $data = TaskAssign::with(['task', 'vendor'])
            ->orderByDesc('id')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('task', fn($row) => optional($row->task)->title ?? '-')
            ->addColumn('vendor', fn($row) => optional($row->vendor)->name ?? '-')
            ->editColumn(
                'finish_date',
                fn($row) =>
                $row->finish_date
                    ? Carbon::parse($row->finish_date)->format('d-m-Y H:i:s')
                    : '-'
            )
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->find(Auth::id());
                $button = '';

                if ($userauth->can('update-taskassignments')) {
                    $button .= ' <a href="' . route('tasks.assign.edit', ['id' => $data->id]) . '" 
                        class="btn btn-sm btn-success action mr-1" 
                        data-id="' . $data->id . '" 
                        data-type="edit" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Edit Data">
                        <i class="fas fa-pencil-alt"></i>
                    </a>';
                }

                if ($userauth->can('delete-taskassignments')) {
                    $button .= ' <button 
                        class="btn btn-sm btn-danger action" 
                        data-id="' . $data->id . '" 
                        data-type="delete" 
                        data-route="' . route('tasks.assign.delete', ['id' => $data->id]) . '" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Delete Data">
                        <i class="fas fa-trash-alt"></i>
                    </button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'task', 'vendor', 'finish_date'])
            ->make(true);
    }

    /**
     * Show create task assignment form
     */
    public function create()
    {
        $data = [
            'tittle' => 'Task Assignments',
            'tasks' => Task::where('status', 'pending')->get(),
            'vendors' => Vendor::all(),
        ];

        return view('pages.tasks.assign.add', $data);
    }

    /**
     * Store a new task assignment
     */
    public function store(Request $request)
    {
        Log::info('Attempting to store new task assignment', $request->all());

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'vendor_id' => 'required|exists:vendors,id',
            'finish_date' => 'nullable|date|after_or_equal:today',
        ]);

        DB::beginTransaction();
        try {
            // Create task assignment
            $taskAssign = TaskAssign::create([
                'task_id' => $validated['task_id'],
                'vendor_id' => $validated['vendor_id'],
                'finish_date' => $validated['finish_date'] ?? null,
                'progress' => 0,
                'notes' => $request->notes ?? null,
            ]);
            Log::info('Task assignment created successfully', ['task_assign_id' => $taskAssign->id]);

            // Update task status based on progress
            $task = Task::findOrFail($validated['task_id']);
            $task->status = $taskAssign->progress === 100 ? 'complated' : 'in_progres';
            $task->save();
            Log::info('Task status updated', ['task_id' => $task->id, 'status' => $task->status]);

            // Update project progress
            $this->updateProjectProgress($task->project_id);

            DB::commit();
            Log::info('Transaction committed successfully');
            return redirect()->route('tasks.assign')->with('status', 'Task assignment successfully created.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to store task assignment', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }

    /**
     * Update task assignment progress
     */

    public function updateProgress(Request $request, $id)
    {
        Log::info('Attempting to update task assignment progress', ['task_assign_id' => $id, 'request' => $request->all()]);

        $validated = $request->validate([
            'progress' => 'required|integer|between:0,100',
        ]);

        DB::beginTransaction();
        try {
            // Find task assignment with its related task
            $taskAssign = TaskAssign::with('task')->findOrFail($id);

            // Ensure task exists
            if (!$taskAssign->task) {
                throw new Exception('No associated task found for this assignment.');
            }

            // Update the progress
            $taskAssign->progress = $validated['progress'];
            $taskAssign->save();
            Log::info('Task assignment progress updated', ['task_assign_id' => $taskAssign->id, 'progress' => $taskAssign->progress]);

            // Update task status
            $task = $taskAssign->task;

            // Determine task status based on progress
            $task->status = match (true) {
                $taskAssign->progress == 0 => 'pending',
                $taskAssign->progress > 0 && $taskAssign->progress < 100 => 'in_progres',
                $taskAssign->progress == 100 => 'complated',
                default => $task->status
            };
            $task->save();
            Log::info('Task status updated', ['task_id' => $task->id, 'status' => $task->status]);

            // Update project progress
            $projectProgress = $this->updateProjectProgress($task->project_id);

            DB::commit();
            Log::info('Transaction committed successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Progress updated successfully.',
                'task_status' => $task->status,
                'project_progress' => $projectProgress
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update progress', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update progress: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete task assignment
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Temukan data task assignment
            $taskAssign = TaskAssign::findOrFail($id);

            // Ambil task terkait
            $task = $taskAssign->task;

            // Set status task menjadi 'pending' jika task ditemukan
            if ($task) {
                $task->status = 'pending';
                $task->save();
                Log::info('Task status updated to pending', ['task_id' => $task->id]);
            }

            // Hapus task assignment
            $taskAssign->delete();
            Log::info('Task assignment deleted', ['task_assign_id' => $taskAssign->id]);

            // Perbarui progress proyek terkait jika ada
            $projectId = $task?->project_id;
            if ($projectId) {
                $this->updateProjectProgress($projectId);
            }

            DB::commit();
            Log::info('Transaction committed successfully');
            return response()->json(['status' => 'success', 'message' => 'Task assignment deleted successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete task assignment', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to delete task assignment: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Update project progress based on task assignments' progress
     */
    private function updateProjectProgress($projectId)
    {
        if (!$projectId) return;

        // Fetch task assignments with their related tasks, filtering for the specific project
        $taskAssignments = TaskAssign::whereHas('task', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })
            ->with('task')  // Eager load the task to avoid N+1 query
            ->get();

        $totalAssignments = $taskAssignments->count();

        // If there are no task assignments, set the project progress to 0
        if ($totalAssignments == 0) {
            return 0;
        }

        // Calculate the total progress of all task assignments
        $totalProgress = $taskAssignments->sum('progress');

        // Calculate average progress for the project
        $averageProgress = $totalProgress / $totalAssignments;

        Log::info('Calculated project progress based on task assignments.', [
            'project_id' => $projectId,
            'total_assignments' => $totalAssignments,
            'total_progress' => $totalProgress,
            'average_progress' => $averageProgress,
        ]);

        return $averageProgress;
    }
}
