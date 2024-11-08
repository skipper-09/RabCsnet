<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['project_id','name','code','description'];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
