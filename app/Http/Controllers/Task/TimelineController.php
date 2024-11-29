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
        // Ambil data project tasks beserta assignee
        $tasks = Task::with(['taskassign','vendor','project'])->get();

        $events = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_date,
                'end' => $task->end_date,
                
            ];
        });
    
        // Ambil data resources (misalnya gedung atau ruangan)
        $resources = Task::with('project', 'vendor')->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'project' => $task->project->name,
                'task' => $task->title,
            ];
        });
    
        return response()->json([
            'events' => $events,
            'resources' => $resources,
        ]);
    }
}
