<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailItemProject extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['detail_id', 'item_id', 'quantity', 'cost_material', 'cost_service'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public function detail()
    {
        return $this->belongsTo(DetailProject::class, 'detail_id', 'id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
