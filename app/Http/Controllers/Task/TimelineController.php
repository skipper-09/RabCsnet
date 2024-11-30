<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Timeline Project'
        ];

        return view('pages.tasks.timelineproject.index', $data);
    }


    public function timeline(Request $request)
{
    // Ambil data project tasks beserta relasi terkait
    $tasks = Task::with(['taskassign', 'vendor', 'project'])->get();

    // Map data tasks menjadi events
    $events = $tasks->map(function ($task) {
        return [
            'id' => $task->id,
            'resourceId' => $task->id, // ID dari task atau sub-task
            'title' => $task->title,
            'start' => $task->start_date,
            'end' => $task->end_date,
            // 'color' => $task->status == 'completed' ? '#28a745' : '#dc3545', // Warna sesuai status
            // 'textColor' => '#ffffff',
        ];
    });

    // Strukturkan resources dengan sub-task
    $resources = $tasks->groupBy('project.id')->map(function ($groupedTasks, $projectId) {
        $projectName = $groupedTasks->first()->project->name;

        return [
            'id' => $projectId, // ID unik untuk project
            'task' => $projectName, // Nama project (task utama)
            'children' => $groupedTasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'task' => $task->title, // Sub-task
                ];
            })->values()->toArray(),
        ];
    })->values()->toArray();

    
    return response()->json([
        'events' => $events,
        'resources' => $resources,
    ]);
}

}
