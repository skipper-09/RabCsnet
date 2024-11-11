<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingAplication extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name','logo','description'];
}
