<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Epc extends Model
{
    protected $fillable = [
        'period_id',
        'description',
        'weight_factor',
        'cum_last_plan',
        'cum_last_actual',
        'cum_last_variance',
        'this_plan',
        'this_actual',
        'cum_now_plan',
        'cum_now_actual',
        'cum_now_variance',
        'status',
        'is_total',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
