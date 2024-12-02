<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['project_id', 'vendor_id', 'parent_id', 'title', 'description', 'start_date', 'end_date', 'status', 'priority', 'complated_date'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function taskassign()
    {
        return $this->hasMany(TaskAssign::class);
    }


    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function mainTask()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function isCompleted()
    {
        return $this->status === 'complated';
    }

    public function progress()
    {
        // Hitung total sub-task
        $totalSubTasks = $this->subTasks()->count();

        // Jika tidak ada sub-task, gunakan status task ini
        if ($totalSubTasks == 0) {
            return $this->status === 'complated' ? 100 : 0;
        }

        // Hitung sub-task yang selesai
        $completedSubTasks = $this->subTasks()->where('status', 'completed')->count();

        // Hitung progres dalam persen
        return ($completedSubTasks / $totalSubTasks) * 100;
    }

    public function getStatusBadge()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="badge badge-soft-primary">Pending</span>';
            case 'in_progres':
                return '<span class="badge badge-soft-info">In Progress</span>';
            case 'complated':
                return '<span class="badge badge-soft-success">Completed</span>';
            default:
                return '<span class="badge badge-soft-danger">Canceled</span>';
        }
    }

    public function getPriorityBadge()
    {
        switch ($this->priority) {
            case 'low':
                return '<span class="badge badge-soft-primary">Low</span>';
            case 'medium':
                return '<span class="badge badge-soft-success">Medium</span>';
            case 'high':
                return '<span class="badge badge-soft-danger">High</span>';
            default:
                return '<span class="badge badge-soft-secondary">Unknown</span>';
        }
    }
}
