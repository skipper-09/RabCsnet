<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'start_status', 'company_id', 'vendor_id', 'responsible_person', 'start_date', 'end_date', 'description', 'status', 'code', 'amount'];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            // Generate item_code if it's not already set
            if (empty($item->code)) {
                $item->code = self::generateItemCode();
            }
        });
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public static function generateItemCode()
    {

        $lastItem = self::orderBy('id', 'desc')->first();
        $lastCode = $lastItem ? $lastItem->code : null;

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 5);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        return 'PRJ-' . $newNumber;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function summary()
    {
        return $this->hasOne(Summary::class);
    }
    public function responsibleperson()
    {
        return $this->belongsTo(User::class, 'responsible_person', 'id');
    }


    public function projectlisence()
    {
        return $this->hasMany(ProjectLisence::class);
    }
    public function Projectfile()
    {
        return $this->hasOne(ProjectFile::class);
    }
    public function Projectatp()
    {
        return $this->hasOne(AtpProject::class);
    }
    public function ProjectReview()
    {
        return $this->hasMany(ProjectReview::class, 'project_id');
    }

    public function detailproject()
    {
        return $this->hasMany(DetailProject::class);
    }

    public function taskdata()
    {
        return $this->hasMany(Task::class);
    }


    public function progress()
    {
        $tasks = $this->taskdata; // Ambil semua tasks terkait
        $totalTasks = $tasks->count();

        // Jika tidak ada task, progres adalah 0
        if ($totalTasks == 0) {
            return 0;
        }

        // Hitung rata-rata progres dari semua main tasks
        $totalProgress = 0;
        $subtask = 0;
        foreach ($tasks as $task) {
            $subtask += $task->subTasks->count();
            $totalProgress += $task->progress();
        }

        // $task = $totalTasks - $subtask;
        return round(($totalProgress / $totalTasks), 2);
    }

}
