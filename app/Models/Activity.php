<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'period_id',
        'scope',
        'location',
        'achievement_this_week',
        'progress',
        'plan_next_week',
        'status',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
