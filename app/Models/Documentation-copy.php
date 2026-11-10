<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Documentation extends Model
{
    protected $fillable = [
        'period_id',
        'file_path',
        'title',
        'description',
    ];

    protected $appends = ['file_url', 'file_type'];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            // Pastikan menggunakan Storage::url dan disk public
            if (Storage::disk('public')->exists($this->file_path)) {
                return Storage::url($this->file_path);
            }
            
            // Fallback jika file tidak ada
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    public function getFileTypeAttribute()
    {
        if (!$this->file_path) return null;
        
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        
        return in_array(strtolower($extension), $imageExtensions) ? 'image' : 'file';
    }

    public function getFileSizeAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            $bytes = Storage::disk('public')->size($this->file_path);
            $units = ['B', 'KB', 'MB', 'GB'];
            $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
            return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
        }
        return null;
    }
}
