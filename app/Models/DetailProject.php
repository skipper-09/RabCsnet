<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailProject extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['project_id','type_project_id','code','name','description'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // Generate item_code if it's not already set
            if (empty($item->code)) {
                $item->code = self::generateItemCode();
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
        $lastCode = $lastItem ? $lastItem->code : null;

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 5);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        return 'DTP-' . $newNumber + rand(100,999);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function projecttype(){
        return $this->belongsTo(ProjectType::class,'type_project_id','id');
    }
    public function detailitemporject(){
        return $this->hasMany(DetailItemProject::class);
    }
}
