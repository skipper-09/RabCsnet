<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['project_id','ppn_id','total_material_cost','total_service_cost','total_ppn_cost','total_summary'];

    public function project(){
        return $this->belongsTo(Project::class,'project_id','id');
    }
    public function ppn(){
        return $this->belongsTo(Ppn::class,'ppn_id','id');
    }
}
