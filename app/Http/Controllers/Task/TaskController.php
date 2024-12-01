<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Vendor;
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

        // Base query for tasks
        $query = Task::with(['project', 'vendor']);

        // Filter tasks based on user role
        if ($currentUserRole === 'Accounting') {
            $query->whereHas('project', function ($query) {
                $query->where('status_pengajuan', 'approved');
            })->orderBy('created_at', 'desc');
        } elseif ($currentUserRole === 'Owner') {
            $query->whereHas('project', function ($query) {
                $query->where('status_pengajuan', 'approved');
            })->orderBy('created_at', 'desc');
        } elseif ($currentUserRole === 'Vendor') {
            $query->where('vendor_id', $currentUser->vendor_id)->whereHas('project', function ($query) {
                $query->where('status_pengajuan', 'approved');
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
                return Carbon::parse($data->start_date)->format('d-m-Y');
            })
            ->editColumn('end_date', function ($data) {
                return Carbon::parse($data->end_date)->format('d-m-Y');
            })->editColumn('status', function ($data) {
                $status = '';

                if ($data->status == 'pending') {
                    $status = '<span class="badge badge-pill badge-soft-primary font-size-13">Pending</span>';
                } else if ($data->status == 'in_progres') {
                    $status = '<span class="badge badge-pill badge-soft-info font-size-13">In Progress</span>';
                } else if ($data->status == 'complated') {
                    $status = '<span class="badge badge-pill badge-soft-success font-size-13">Completed</span>';
                } else {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-13">Canceled</span>';
                }

                return $status;
            })->editColumn('priority', function ($data) {
                $priority = '';
                if ($data->priority == 'low') {
                    $priority = '<span class="badge badge-pill badge-soft-primary font-size-13">Low</span>';
                } else if ($data->priority == 'medium') {
                    $priority = '<span class="badge badge-pill badge-soft-success font-size-13">Medium</span>';
                } else {
                    $priority = '<span class="badge badge-pill badge-soft-danger font-size-13">High</span>';
                }
                return $priority;
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-tasks')) {
                    $button .= ' <a href="' . route('tasks.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                    class="fas fa-pencil-alt"></i></a>';
                }
                if ($userauth->can('delete-tasks')) {
                    $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('tasks.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                    class="fas fa-trash-alt "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'project', 'vendor', 'start_date', 'end_date', 'status', 'priority'])
            ->make(true);
    }

    public function create()
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()->name;

        $baseData = [
            'tittle' => 'Task',
            'parentTasks' => Task::whereNull('parent_id')->get(),
        ];

        // Vendors logic based on user role
        if (in_array($currentUserRole, ['Accounting', 'Owner', 'Developer'])) {
            $baseData['projects'] = Project::where('status_pengajuan', 'approved')->get();
        } else {
            // For Vendor role or other roles
            $baseData['projects'] = Project::where('status_pengajuan', 'approved')
                ->where('vendor_id', $currentUser->vendor_id)
                ->get();
        }

        return view('pages.tasks.add', $baseData);
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
                'status' => 'pending',
                'priority' => $request->priority,
                'parent_id' => $request->parent_id,
            ]);

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

        // Base data for the view
        $baseData = [
            'tittle' => 'Task',
            'tasks' => Task::findOrFail($id),
            'parentTasks' => Task::whereNull('parent_id')->get(),
        ];

        // Projects logic based on user role
        if (in_array($currentUserRole, ['Accounting', 'Owner', 'Developer'])) {
            $baseData['projects'] = Project::where('status_pengajuan', 'approved')->get();
        } else {
            // For Vendor role or other roles
            $baseData['projects'] = Project::where('status_pengajuan', 'approved')
                ->where('vendor_id', $currentUser->vendor_id)
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
            'status' => 'required|in:pending,in_progres,complated,canceled',
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
}
