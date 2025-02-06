<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportImage extends Model
{
    protected $fillable = ['image', 'report_id'];

    // Relationship to ReportVendor
    public function reportVendor()
    {
        return $this->belongsTo(ReportVendor::class, 'report_id');
    }
}
