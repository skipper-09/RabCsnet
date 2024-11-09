<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeItem extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
