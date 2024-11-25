<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Summary extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['project_id','total_material_cost','total_service_cost','total_ppn_cost','total_summary'];

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
        return $this->belongsTo(Project::class,'project_id','id');
    }
    public function ppn(){
        return $this->belongsTo(Ppn::class,'ppn_id','id');
    }
}
