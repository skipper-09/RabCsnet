<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ppn extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $fillable = ['name','count','description'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }
    public function summary(){
        return $this->hasMany(Summary::class);
    }
}
