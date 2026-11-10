<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'name', 'email', 'password',
        'role',
        'job_position', 'status', 'approved_at', 'approved_by', 'avatar_url',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    public function approvedBy()
    {
        // Relasi ke sesama tabel users (Self Join)
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Role Checkers Sederhana
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    // Approval Logic Sederhana
    public function canApprove($status)
    {
        // Admin hanya bisa approve jika statusnya 'submitted'
        return $this->isAdmin() && $status === 'submitted';
    }

    public function getNextStatus($currentStatus)
    {
        // Flow: saved -> submitted -> approved
        if ($currentStatus === 'saved' || $currentStatus === 'rejected') {
            return 'submitted';
        }
        if ($currentStatus === 'submitted') {
            return 'approved';
        }
        return $currentStatus;
    }

    public function getDisplayStatus($status)
    {
        $displayStatuses = [
            'saved' => 'Draft',
            'submitted' => 'Menunggu Review Admin',
            'approved' => 'Approved',
            'rejected' => 'Perlu Revisi'
        ];

        return $displayStatuses[$status] ?? ucfirst($status);
    }
}