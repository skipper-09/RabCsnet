<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailProject extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['project_id','type_project_id','code','name','description'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function projecttype(){
        return $this->belongsTo(ProjectType::class);
    }
    public function detailitemporject(){
        return $this->hasMany(DetailItemProject::class);
    }
}
