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

        $tasks = Task::with(['subTasks'])->get();

        // Map data tasks menjadi events
        $events = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'resourceId' => $task->id,
                'title' => $task->title,
                'start' => $task->start_date,
                'end' => $task->end_date,
                'color' => $color = match ($task->status) {
                    'complated' => '#34c38f',
                    'pending' => '#f1b44c',
                    'overdue' => '#f46a6a',
                    default => '#50a5f1',
                },
                'textColor' => '#ffffff',
            ];
        });

        $resources = $tasks->map(function ($task) {
            $subTasks = $task->subTasks;
    
            // Total subtask yang selesai
            $completedSubTasksCount = $subTasks->where('status', 'complated')->count();
            $totalSubTasks = $subTasks->count();
    
            // Hitung progres main task berdasarkan bobot subtasks
            $mainTaskProgress = $totalSubTasks > 0
                ? round(($completedSubTasksCount / $totalSubTasks) * 100, 2)
                : ($task->status === 'complated' ? 100 : 0);
    
            return [
                'id' => $task->id,
                'task' => $task->title,
                'progress' => $mainTaskProgress . '%',
                'children' => $subTasks->map(function ($subTask) use ($completedSubTasksCount, $totalSubTasks) {
                    $progressPercentage = $subTask->status === 'complated'
                        ? round(100 / $totalSubTasks, 2)
                        : 0; // Jika tidak selesai, progress = 0
                    return [
                        'id' => $subTask->id,
                        'task' => $subTask->title,
                        'progress' => $progressPercentage . '%',
                    ];
                })->toArray(),
            ];
        })->toArray();
    
        return response()->json([
            'events' => $events,
            'resources' => $resources,
        ]);
    }

}
