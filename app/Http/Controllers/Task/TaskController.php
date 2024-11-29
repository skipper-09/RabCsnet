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
        // Get task data that contain project status is approved
        $dataType = Task::with(['project', 'vendor'])->whereHas('project', function ($query) {
            $query->where('status_pengajuan', 'approved');
        })->orderByDesc('id')->get();

        return DataTables::of($dataType)
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
        $data = [
            'tittle' => 'Task',
            'projects' => Project::where('status_pengajuan', 'approved')->get(), // Changed from Task to Project
            'vendors' => Vendor::all(),
        ];

        return view('pages.tasks.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high',
        ], [
            'project_id.required' => 'Project is required',
            'project_id.exists' => 'Project not found',
            'vendor_id.required' => 'Vendor is required',
            'vendor_id.exists' => 'Vendor not found',
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

        try {
            Task::create([
                'project_id' => $request->project_id,
                'vendor_id' => $request->vendor_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'pending',
                'priority' => $request->priority,
            ]);

            return redirect()->route('tasks')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Task!']);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add data: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function show($id)
    {
        $data = [
            'tittle' => 'Task',
            'tasks' => Task::findOrFail($id), // Changed from 'tasks' to 'task'
            'projects' => Project::where('status_pengajuan', 'approved')->get(),
            'vendors' => Vendor::all(),
        ];

        return view('pages.tasks.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progres,complated,canceled',
            'priority' => 'required|in:low,medium,high',
        ], [
            'project_id.required' => 'Project is required',
            'project_id.exists' => 'Project not found',
            'vendor_id.required' => 'Vendor is required',
            'vendor_id.exists' => 'Vendor not found',
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

        try {
            $tasks = Task::findOrFail($id); // Added error handling

            $tasks->update([
                'project_id' => $request->project_id,
                'vendor_id' => $request->vendor_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'priority' => $request->priority,
            ]);

            return redirect()->route('tasks')->with(['status' => 'Success', 'message' => 'Berhasil Mengupdate Task!']);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update data');
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
