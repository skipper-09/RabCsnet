<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Distribution_item extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $fillable = ['distribution_id','item_id','material_count'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }
    public function distribution(){
        return $this->belongsTo(Distribution::class,'distribution_id','id');
    }
    public function item(){
        return $this->belongsTo(Item::class,'item_id','id');
    }
}
