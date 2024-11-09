<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name','type_id','item_code','unit_id','material_price','service_price','description'];

    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id','id');
    }
    public function typeItem(){
        return $this->belongsTo(TypeItem::class, 'type_id','id');
    }
    public function disttibusiItem(){
        return $this->hasMany(Distribution_item::class);
    }
}
