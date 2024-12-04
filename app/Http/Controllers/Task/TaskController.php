<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\ReportVendor;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Tasks'
        ];

        return view('pages.tasks.index', $data);
    }

    public function getData(Request $request)
    {
        // Get the authenticated user
        $currentUser = Auth::user();

        $currentUserRole = $currentUser->roles->first()->name;

        $vendor = Vendor::where('user_id', $currentUser->id)->first();

        // Base query for tasks
        $query = Task::with(['project', 'vendor', 'subTasks'])->whereNull('parent_id');

        // Hitung progres untuk setiap main task
        $totalProgress = $query->get()->map(function ($task) {
            return $task->progress();
        })->avg();

        // Filter tasks based on user role
        if ($currentUserRole === 'Accounting') {
            $query->whereHas('project', function ($query) {
                $query->where('start_status', 1);
            })->orderBy('created_at', 'desc');
        } elseif ($currentUserRole === 'Owner') {
            $query->whereHas('project', function ($query) {
                $query->where('start_status', 1);
            })->orderBy('created_at', 'desc');
        } elseif ($currentUserRole === 'Vendor') {
            $query->where('vendor_id',  $vendor->id)
                ->whereHas('project', function ($query) {
                    $query->where('start_status', 1);
                })->orderBy('created_at', 'desc');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('project', function ($row) {
                return $row->project ? $row->project->name : '-';
            })
            ->addColumn('vendor', function ($row) {
                return $row->vendor ? $row->vendor->name : '-';
            })
            ->editColumn('start_date', function ($data) {
                return $data->start_date ? Carbon::parse($data->start_date)->format('d-m-Y') : '-';
            })
            ->editColumn('end_date', function ($data) {
                return $data->end_date ? Carbon::parse($data->end_date)->format('d-m-Y') : '-';
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusBadge();
            })
            ->editColumn('priority', function ($data) {
                return $data->getPriorityBadge();
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                // Show details button only for main tasks with subtasks
                if ($data->parent_id === null && $data->subTasks->count() > 0) {
                    $button .= ' <a href="' . route('tasks.details', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="details" data-toggle="tooltip" data-placement="bottom" title="View Details"><i class="fas fa-eye"></i></a>';
                }

                // Show button with icons for main tasks without subtasks
                if ($data->parent_id === null && $data->subTasks->count() === 0) {
                    // Cek apakah pengguna memiliki izin untuk memperbarui tugas
                    $isDisabled = $userauth->can('complete-tasks') ? '' : 'disabled';

                    // Periksa status tugas
                    $isInProgress = $data->status === 'in_progres'; // Tombol hanya akan tampil jika statusnya 'in_progress'

                    // Jika statusnya 'in_progress', tampilkan tombol
                    if ($isInProgress) {
                        $button .= '<button type="button" class="btn btn-sm btn-success task-completion-button" 
                                    data-id="' . $data->id . '" 
                                    ' . $isDisabled . ' data-toggle="tooltip" data-placement="bottom" title="Complete Task">
                                    <i class="fas fa-check"></i> 
                                </button>';
                    } else {
                        // Jika statusnya bukan 'in_progress', tampilkan tombol untuk membatalkan penyelesaian
                        $button .= '<button type="button" class="btn btn-sm btn-danger task-completion-button" 
                                    data-id="' . $data->id . '" 
                                    ' . $isDisabled . ' data-toggle="tooltip" data-placement="bottom" title="Uncomplete Task">
                                    <i class="fas fa-times"></i> 
                                </button>';
                    }
                }

                // Edit button
                if ($userauth->can('update-tasks')) {
                    $button .= ' <a href="' . route('tasks.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }

                // Delete button
                if ($userauth->can('delete-tasks')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('tasks.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i class="fas fa-trash-alt "></i></button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })

            ->rawColumns(['action', 'project', 'vendor', 'start_date', 'end_date', 'status', 'priority', 'parent_tasks'])
            ->make(true);
    }

    public function details($id)
    {
        $currentUser = Auth::user();
        $vendor = Vendor::where('user_id', $currentUser->id)->first();

        if ($vendor) {
            $task = Task::with([
                'project',
                'vendor',
                'subTasks' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'mainTask',
                // 'taskassign.user'
            ])->where('vendor_id', $vendor->id)->findOrFail($id);
        } else {
            $task = $task = Task::with([
                'project',
                'vendor',
                'subTasks' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'mainTask',
                // 'taskassign.user'
            ])->findOrFail($id);
        }

        // Calculate overall task progress
        $totalSubTasks = $task->subTasks->count();
        $completedSubTasks = $task->subTasks->where('status', 'complated')->count();
        $progressPercentage = $totalSubTasks > 0
            ? round(($completedSubTasks / $totalSubTasks) * 100, 2)
            : ($task->status === 'complated' ? 100 : 0);

        // Prepare assigned users
        // $assignedUsers = $task->taskassign->map(function ($assignment) {
        //     return $assignment->user;
        // });

        // Determine if the task is a subtask
        $isSubTask = $task->parent_id !== null;
        $parentTask = $isSubTask ? $task->mainTask : null;

        $data = [
            'tittle' => "Task {$task->title} Details",
            'task' => $task,
            'progressPercentage' => $progressPercentage,
            // 'assignedUsers' => $assignedUsers,
            'isSubTask' => $isSubTask,
            'parentTask' => $parentTask
        ];

        return view('pages.tasks.detail', $data);
    }

    public function create()
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()->name;

        $baseData = [
            'tittle' => 'Task',
            'parentTasks' => collect(), // Inisialisasi collection kosong
        ];

        $projectQuery = Project::where('start_status', 1)->with('vendor');

        if (in_array($currentUserRole, ['Accounting', 'Owner', 'Developer'])) {
            $baseData['projects'] = $projectQuery->get();
        } else {
            $baseData['projects'] = $projectQuery
                ->where('vendor_id', $currentUser->vendor_id)
                ->get();
        }

        return view('pages.tasks.add', $baseData);
    }

    // Metode baru untuk mengambil parent tasks berdasarkan vendor_id project
    public function getParentTasksByProjectVendor($projectId)
    {
        // Ambil project untuk mendapatkan vendor_id
        $project = Project::findOrFail($projectId);

        // Ambil parent tasks sesuai vendor_id project yang dipilih
        $parentTasks = Task::whereNull('parent_id')
            ->where('vendor_id', $project->vendor_id)
            ->where('project_id', $projectId)
            ->with('project', 'vendor')
            ->get();

        // Filter out tasks yang sudah completed
        $filteredParentTasks = $parentTasks->filter(function ($task) {
            return $task->status !== 'complated';
        });

        return response()->json($filteredParentTasks->values());
    }

    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high',
            'parent_id' => 'nullable|exists:tasks,id'
        ], [
            'project_id.required' => 'Project is required',
            'project_id.exists' => 'Project not found',
            'title.required' => 'Title is required',
            'title.max' => 'Title is too long',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date is invalid',
            'end_date.required' => 'End date is required',
            'end_date.date' => 'End date is invalid',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Priority is invalid',
        ]);

        // Retrieve the project
        $project = Project::findOrFail($request->project_id);

        // Get vendor_id from the project
        $vendor_id = $project->vendor_id;

        // Validate project dates
        $this->validateProjectDates($project, $request->start_date, $request->end_date);

        // Current authenticated user
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()->name;

        // Additional vendor validation for non-admin roles
        if (!in_array($currentUserRole, ['Accounting', 'Owner', 'Developer'])) {
            if ($vendor_id != $currentUser->vendor_id) {
                return redirect()->back()
                    ->with('error', 'You are not authorized to create tasks for this vendor.')
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            $task = Task::create([
                'project_id' => $request->project_id,
                'vendor_id' => $vendor_id, // Use vendor_id from project
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'in_progres',
                'priority' => $request->priority,
                'parent_id' => $request->parent_id,
            ]);

            // If this is a sub-task and the parent is completed, update parent status
            if ($request->parent_id) {
                $parentTask = Task::find($request->parent_id);
                if ($parentTask && $parentTask->status === 'complated') {
                    $parentTask->status = 'in_progres';
                    $parentTask->complated_date = null;
                    $parentTask->save();
                }
            }

            // Log task creation
            \Log::info('Task Created Successfully', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'created_by' => $currentUser->id
            ]);

            DB::commit();

            return redirect()->route('tasks')
                ->with('status', 'Success')
                ->with('message', 'Successfully Added Task!');
        } catch (Exception $e) {
            DB::rollBack();

            \Log::error('Task Creation Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => $currentUser->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Validate project dates against task dates
     *
     * @param Project $project
     * @param string $taskStartDate
     * @param string $taskEndDate
     * @throws \Exception
     */
    private function validateProjectDates($project, $taskStartDate, $taskEndDate)
    {
        // Check if project has start and end dates
        if (is_null($project->start_date) || is_null($project->end_date)) {
            throw new Exception('The selected project does not have both start date and end date set.');
        }

        // Convert dates to timestamps for comparison
        $projectStartTimestamp = strtotime($project->start_date);
        $projectEndTimestamp = strtotime($project->end_date);
        $taskStartTimestamp = strtotime($taskStartDate);
        $taskEndTimestamp = strtotime($taskEndDate);

        // Validate task dates are within project dates
        if ($taskStartTimestamp < $projectStartTimestamp) {
            throw new Exception('The start date of the task cannot be earlier than the project start date.');
        }

        if ($taskEndTimestamp > $projectEndTimestamp) {
            throw new Exception('The end date of the task cannot be later than the project end date.');
        }
    }

    public function show($id)
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()->name;

        // Ambil task saat ini
        $currentTask = Task::findOrFail($id);

        // Data dasar untuk tampilan
        $baseData = [
            'tittle' => 'Task',
            'tasks' => $currentTask,
            'parentTasks' => collect(), // Inisialisasi collection kosong
        ];

        // Query proyek berdasarkan start_status
        $projectQuery = Project::where('start_status', 1)->with('vendor');

        if (in_array($currentUserRole, ['Accounting', 'Owner', 'Developer'])) {
            $baseData['projects'] = $projectQuery->get();
        } else {
            $baseData['projects'] = $projectQuery
                ->where('vendor_id', $currentUser->vendor_id)
                ->get();
        }

        // Jika task saat ini memiliki project_id, ambil parent tasks sesuai vendor_id
        if ($currentTask->project_id) {
            $project = Project::findOrFail($currentTask->project_id);

            $baseData['parentTasks'] = Task::whereNull('parent_id')
                ->where('vendor_id', $project->vendor_id)
                ->with('project', 'vendor')
                ->get();
        }

        return view('pages.tasks.edit', $baseData);
    }


    public function update(Request $request, $id)
    {
        // Validate input
        $validatedData = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progres,complated',
            'priority' => 'required|in:low,medium,high',
            'parent_id' => 'nullable|exists:tasks,id',
        ], [
            'project_id.required' => 'Project is required',
            'project_id.exists' => 'Project not found',
            'title.required' => 'Title is required',
            'title.max' => 'Title is too long',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date is invalid',
            'end_date.required' => 'End date is required',
            'end_date.date' => 'End date is invalid',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'status.required' => 'Status is required',
            'status.in' => 'Status is invalid',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Priority is invalid',
        ]);

        // Retrieve the task and project
        $task = Task::findOrFail($id);
        $project = Project::findOrFail($request->project_id);

        // Get vendor_id from the project
        $vendor_id = $project->vendor_id;

        // Current authenticated user
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()->name;

        // Additional vendor validation for non-admin roles
        if (!in_array($currentUserRole, ['Accounting', 'Owner', 'Developer'])) {
            if ($vendor_id != $currentUser->vendor_id) {
                return redirect()->back()
                    ->with('error', 'You are not authorized to update tasks for this vendor.')
                    ->withInput();
            }
        }

        // Validate project dates
        $this->validateProjectDates($project, $request->start_date, $request->end_date);

        try {
            DB::beginTransaction();

            $task->update([
                'project_id' => $request->project_id,
                'vendor_id' => $vendor_id, // Use vendor_id from project
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'priority' => $request->priority,
                'parent_id' => $request->parent_id,
            ]);

            // Jika ini adalah task utama (parent task)
            if (!$task->parent_id) {
                // Jika task utama sudah complated, maka semua subtask ikut complated
                if ($task->status === 'complated') {
                    $task->subTasks()->update([
                        'status' => 'complated',
                        'complated_date' => now()
                    ]);
                }
                // Jika task utama tidak complated, reset status subtask
                else {
                    $task->subTasks()->update([
                        'status' => 'in_progres',
                        'complated_date' => null
                    ]);
                }
            }

            // Log task update
            \Log::info('Task Updated Successfully', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'updated_by' => $currentUser->id
            ]);

            DB::commit();

            return redirect()->route('tasks')
                ->with('status', 'Success')
                ->with('message', 'Successfully Updated Task!');
        } catch (Exception $e) {
            DB::rollBack();

            \Log::error('Task Update Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => $currentUser->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $tasks = Task::findOrFail($id);

            $tasks->delete();

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

    public function toggleCompletion($id)
    {
        DB::beginTransaction();

        try {
            // Find the task with a lock to prevent concurrent modifications
            $task = Task::lockForUpdate()->findOrFail($id);

            // Current authenticated user
            $currentUser = Auth::user();

            // Check if user has permission to complete tasks
            if (!$currentUser->can('complete-tasks')) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to complete this task.'
                ], 403);
            }

            // Store the original status for logging
            $originalStatus = $task->status;

            // Correct the status values and implement a more robust toggle
            $task->status = $task->status !== 'complated' ? 'complated' : 'in_progres';
            $task->complated_date = $task->status === 'complated' ? now() : null;

            // Save the task
            $task->save();

            // Manage report vendor based on task status
            $this->manageReportVendor($task);

            // Log the status change
            activity()
                ->performedOn($task)
                ->causedBy($currentUser)
                ->withProperties([
                    'old_status' => $originalStatus,
                    'new_status' => $task->status
                ])
                ->log('Task completion status toggled');

            // Handle sub-tasks and parent task progress
            $this->handleTaskProgressAndSubTasks($task);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Task completion status updated successfully.',
                'task_status' => $task->status,
                'completed' => $task->status === 'complated'
            ]);
        } catch (Exception $e) {
            // Rollback the transaction
            DB::rollBack();

            // Log the error
            \Log::error('Task Completion Toggle Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'task_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update task completion status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function manageReportVendor($task)
    {
        if ($task->status === 'complated') {
            // Create report vendor only if it doesn't already exist
            ReportVendor::firstOrCreate(
                [
                    'task_id' => $task->id
                ],
                [
                    'project_id' => $task->project_id,
                    'vendor_id' => $task->vendor_id,
                    'title' => $task->title,
                    // You can add more default values if needed
                ]
            );
        } else {
            // Delete existing report vendor when task is uncompleted
            ReportVendor::where('task_id', $task->id)->delete();
        }
    }

    /**
     * Handle task progress for parent and sub-tasks
     * 
     * @param Task $task
     */
    private function handleTaskProgressAndSubTasks($task)
    {
        // If this task has a parent, update parent task status only if it's a subtask
        if ($task->parent_id) {
            // This is a subtask, so don't update parent task progress directly
            $parentTask = Task::find($task->parent_id);
            if ($parentTask) {
                // Check the number of subtasks
                $subTasks = $parentTask->subTasks;

                // Check if all existing sub-tasks are completed
                $allSubTasksCompleted = $subTasks->every(function ($subTask) {
                    return $subTask->status === 'complated';
                });

                // If there's a new sub-task and parent is already completed, change status to in_progres
                if (!$allSubTasksCompleted) {
                    // Update parent task status to in_progres
                    $parentTask->status = 'in_progres';
                    $parentTask->complated_date = null;
                    $parentTask->save();
                } elseif ($allSubTasksCompleted) {
                    // Update parent task status to completed
                    $parentTask->status = 'complated';
                    $parentTask->complated_date = now();
                    $parentTask->save();
                }
            }
        }

        // If this task has sub-tasks, update their status accordingly (only if it's a parent task)
        if ($task->status === 'complated' && !$task->parent_id) {
            // Mark all existing subtasks as completed when parent task is completed
            $task->subTasks()->update([
                'status' => 'complated',
                'complated_date' => now()
            ]);
        } elseif ($task->status !== 'complated' && !$task->parent_id) {
            // If parent task is uncompleted, reset subtask statuses
            $task->subTasks()->update([
                'status' => 'in_progres',
                'complated_date' => null
            ]);
        }
    }
}
