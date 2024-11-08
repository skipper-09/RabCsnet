<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ppn extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name','count','description'];

    public function summary(){
        return $this->hasMany(Summary::class);
    }
}
