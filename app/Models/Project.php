<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name','company_id','responsible_person','start_date','end_date','description','status'];

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
