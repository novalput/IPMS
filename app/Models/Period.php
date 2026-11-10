<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'name', 'start_date', 'end_date',
        'created_by', 'updated_by', 'status', 'is_completed', 'completed_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectProgress()
    {
        return $this->hasOne(ProjectProgress::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Data relations
    public function epc()
    {
        return $this->hasMany(\App\Models\Epc::class);
    }

    public function engineerings()
    {
        return $this->hasMany(\App\Models\Engineering::class);
    }

    public function procurementMaterials()
    {
        return $this->hasMany(\App\Models\ProcurementMaterial::class);
    }

    public function procurementServices()
    {
        return $this->hasMany(\App\Models\ProcurementService::class);
    }

    public function constructions()
    {
        return $this->hasMany(\App\Models\Construction::class);
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\Activity::class);
    }

    public function issues()
    {
        return $this->hasMany(\App\Models\Issue::class);
    }

    public function documentations()
    {
        return $this->hasMany(\App\Models\Documentation::class);
    }

    // Permission methods
    public function canBeEditedBy($user)
    {
        // Admin bisa edit kapan saja (opsional: atau batasi jika sudah approved)
        if ($user->isAdmin()) return true;

        // User hanya bisa edit milik sendiri DAN statusnya masih Draft atau Rejected
        if ($user->isUser()) {
            return $this->created_by === $user->id && in_array($this->status, ['saved', 'rejected']);
        }

        return false;
    }

    public function canBeApprovedBy($user)
    {
        return $user->isAdmin() && $this->status === 'submitted';
    }

    public function getDisplayStatusForUser($user)
    {
        // Logic tampilan status sederhana
        switch($this->status) {
            case 'saved':
                return 'Draft';
            case 'submitted':
                return 'Submitted';
            case 'approved':
                // Opsional: ambil nama admin dari tracking terakhir
                return 'Approved';
            case 'rejected':
                return 'Rejected';
            default:
                return ucfirst($this->status);
        }
    }

    public function getStatusAfterReject()
    {
        // Jika direject, kembali ke status 'saved' (atau 'rejected' jika ingin status khusus)
        // Disini saya pakai 'saved' agar user bisa langsung edit lagi
        return 'rejected'; 
    }

    public function getStatusBadgeClass()
    {
        $classes = [
            'saved' => 'status-draft',      // abu-abu/kuning
            'submitted' => 'status-submitted', // biru
            'approved' => 'status-approved',   // hijau
            'rejected' => 'status-rejected'    // merah
        ];

        return $classes[$this->status] ?? 'status-draft';
    }

    // Helper untuk mendapatkan status yang dapat dibaca
    public function getReadableStatus()
    {
        $statuses = [
            'saved' => 'Tersimpan Draft',
            'submitted' => 'Menunggu Approval',
            'approved' => 'Sudah Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
    
    public function scopeWithUsers($query)
    {
        return $query->with(['createdBy', 'updatedBy']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_completed', false);
    }
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

}
