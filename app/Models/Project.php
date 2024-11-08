<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name','company_id','responsible_person','start_date','end_date','description','status'];
}
