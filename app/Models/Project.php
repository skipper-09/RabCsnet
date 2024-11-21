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
    protected $fillable = ['name','company_id','responsible_person','start_date','end_date','description','status'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }



    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function distribusi(){
        return $this->hasMany(Distribution::class);
    }
    public function summary(){
        return $this->hasMany(Summary::class);
    }
}
