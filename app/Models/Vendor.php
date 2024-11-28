<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name','code','phone','email','address','website','status','user_id'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });


        static::creating(function ($item) {
            // Generate item_code if it's not already set
            if (empty($item->code)) {
                $item->code = self::generateItemCode();
            }
        });
    }

    public static function generateItemCode()
    {

        $lastItem = self::orderBy('id', 'desc')->first();
        $lastCode = $lastItem ? $lastItem->code : null;

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 5);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        return 'VND-' . $newNumber + rand(10000,99999);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
