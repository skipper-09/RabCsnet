<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name','id_type','item_code','unit_id','material_price','service_price','description'];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
    public function TypeItem(){
        return $this->belongsTo(TypeItem::class);
    }
}
