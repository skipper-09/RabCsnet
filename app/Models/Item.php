<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Item extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'type_id', 'item_code', 'unit_id', 'material_price', 'service_price', 'description'];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            // Generate item_code if it's not already set
            if (empty($item->item_code)) {
                $item->item_code = self::generateItemCode();
            }
        });

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public static function generateItemCode()
    {

        $lastItem = self::orderBy('id', 'desc')->first();
        $lastCode = $lastItem ? $lastItem->item_code : null;

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 4);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'ITM-' . $newNumber;
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function type()
    {
        return $this->belongsTo(TypeItem::class);
    }
    public function disttibusiItem()
    {
        return $this->hasMany(Distribution_item::class);
    }
}
