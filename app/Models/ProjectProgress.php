<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProgress extends Model
{
    use HasFactory;

    protected $table = 'project_progress';

    protected $fillable = [
        'period_id',
        'progress_percentage',
        'weight',
        'notes',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    // Relationships
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function project()
    {
        return $this->hasOneThrough(
            Project::class,
            Period::class,
            'id', // Foreign key on periods table
            'id', // Foreign key on projects table
            'period_id', // Local key on project_progress table
            'project_id' // Local key on periods table
        );
    }

    // Helper methods
    public function getStatusAttribute()
    {
        if ($this->progress_percentage >= 100) {
            return 'completed';
        } elseif ($this->progress_percentage >= 75) {
            return 'on-track';
        } elseif ($this->progress_percentage >= 50) {
            return 'in-progress';
        } else {
            return 'delayed';
        }
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => '#28a745',
            'on-track' => '#17a2b8',
            'in-progress' => '#ffc107',
            'delayed' => '#dc3545',
            default => '#6c757d'
        };
    }
}
?>