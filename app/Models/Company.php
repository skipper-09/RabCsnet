<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['company','address','phone'];

    public function project(){
        return $this->hasMany(Project::class);
    }
}