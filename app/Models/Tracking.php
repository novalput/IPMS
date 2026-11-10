<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $fillable = [
        'period_id',
        'issued_date',
        'sender',
        'recipient', 
        'subject',
        'status',
        'reviewer',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function project()
    {
        return $this->hasOneThrough(Project::class, Period::class, 'id', 'id', 'period_id', 'project_id');
    }

    public static function getJobPositionByRole($role)
    {
        $user = \App\Models\User::where('role', $role)->first();
        return $user ? $user->job_position : null;
    }

    public static function formatRoleWithJobPosition($role)
    {
        $jobPosition = self::getJobPositionByRole($role);
        return $jobPosition ? "$jobPosition" : $role;
    }
}
