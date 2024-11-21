<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Distribution extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $fillable = ['project_id','name','code','description'];

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
    public function distributionItems(){
        return $this->hasMany(Distribution_item::class);
    }
}
