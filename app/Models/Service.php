<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';

    protected $fillable = ['service_code', 'name', 'price'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($service) {
            // Generate service_code if it's not already set
            if (empty($service->service_code)) {
                $service->service_code = self::generateServiceCode();
            }
        });

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public static function generateServiceCode()
    {

        $lastItem = self::orderBy('id', 'desc')->first();
        $lastCode = $lastItem ? $lastItem->service_code : null;

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 4);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'SRV-' . $newNumber;
    }
}
