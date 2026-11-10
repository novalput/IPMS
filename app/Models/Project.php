<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
    ];

    // Relasi dengan Period
    public function periods()
    {
        return $this->hasMany(Period::class);
    }


    // Helper list flattened
    public function periodsWithStatus()
    {
        return $this->periods()->select('id','project_id','name','status','created_by','updated_by','start_date','end_date')->get();
    }

    // Relasi dengan ProjectProgress
    public function projectProgress()
    {
        return $this->hasMany(ProjectProgress::class);
    }

    // Mendapatkan progress terbaru
    public function getLatestProgressAttribute()
    {
        return $this->projectProgress()->latest()->first();
    }

    // Mendapatkan riwayat progress
    public function getProgressHistoryAttribute()
    {
        return $this->projectProgress()->orderBy('created_at')->get();
    }

    // Mendapatkan progress berdasarkan periode
    public function getProgressByPeriodAttribute()
    {
        return $this->projectProgress()
            ->with('period')
            ->orderBy('created_at')
            ->get()
            ->groupBy('period_id');
    }
}
