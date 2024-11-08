<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution_item extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['distribution_id','item_id','material_count'];
    public function distribution(){
        return $this->belongsTo(Distribution::class,'distribution_id','id');
    }
    public function item(){
        return $this->belongsTo(Item::class,'item_id','id');
    }
}
