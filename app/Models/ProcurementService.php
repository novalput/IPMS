<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementService extends Model
{
    protected $fillable = [
        'period_id',
        'service_name',
        'po_number',
        'po_issued',
        'mobilization',
        'field_work',
        'reporting',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
