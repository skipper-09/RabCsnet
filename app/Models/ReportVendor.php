<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportVendor extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'project_id', 'vendor_id', 'task_id', 'title', 'description', 'issue'
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate UUID for the report if it doesn't have one
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // One-to-many relationship with ReportImage (via report_vendors)
    public function reportImages()
    {
        return $this->hasMany(ReportImage::class, 'report_id');
    }
}
