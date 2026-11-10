<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'period_id',
        'name',
        'location',
        'equipment',
        'description',
        'status',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
