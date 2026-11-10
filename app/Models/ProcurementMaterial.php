<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementMaterial extends Model
{
    protected $fillable = [
        'period_id',
        'material_name',
        'po_number',
        'po_issued',
        'submit_tpg',
        'approve_tpg',
        'manufacture',
        'fat',
        'delivery',
        'material_on_site',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
