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

    protected $appends = ['file_url', 'file_size_formatted'];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        if (app()->environment('production')) {
            // Untuk production di cPanel
            $relativePath = $this->file_path;
            
            // Hapus 'public/' prefix jika ada
            if (str_starts_with($relativePath, 'public/')) {
                $relativePath = str_replace('public/', '', $relativePath);
            }
            
            // Untuk file lama yang tersimpan dengan path 'documentation/...'
            // Redirect ke 'images/documentation/...'
            if (str_starts_with($relativePath, 'documentation/') && !str_starts_with($relativePath, 'images/documentation/')) {
                $relativePath = str_replace('documentation/', 'images/documentation/', $relativePath);
            }
            
            // Buat URL dengan public/storage prefix
            $url = asset('public/storage/' . $relativePath);
            
            // Test apakah file benar-benar ada
            $fullPath = public_path('storage/' . $relativePath);
            $fileExists = file_exists($fullPath);
            
            // Debug log
            \Log::info('File URL Debug', [
                'original_path' => $this->file_path,
                'relative_path' => $relativePath,
                'final_url' => $url,
                'full_path' => $fullPath,
                'file_exists' => $fileExists
            ]);
            
            return $url;
        } else {
            // Untuk local development
            return Storage::url($this->file_path);
        }
    }

    public function getFileSizeFormattedAttribute()
    {
        $filePath = $this->file_path;
        
        if (!$filePath) return 'Unknown';
        
        try {
            if (app()->environment('production')) {
                // Untuk production
                $relativePath = $filePath;
                
                if (str_starts_with($relativePath, 'public/')) {
                    $relativePath = str_replace('public/', '', $relativePath);
                }
                
                // Fix path untuk file lama
                if (str_starts_with($relativePath, 'documentation/') && !str_starts_with($relativePath, 'images/documentation/')) {
                    $relativePath = str_replace('documentation/', 'images/documentation/', $relativePath);
                }
                
                $fullPath = public_path('storage/' . $relativePath);
                
                if (file_exists($fullPath)) {
                    $bytes = filesize($fullPath);
                } else {
                    return 'File not found';
                }
            } else {
                // Untuk local development
                if (Storage::disk('public')->exists($filePath)) {
                    $bytes = Storage::disk('public')->size($filePath);
                } else {
                    return 'File not found';
                }
            }
            
            if ($bytes == 0) return '0 Bytes';
            
            $units = ['Bytes', 'KB', 'MB', 'GB'];
            $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
            return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
            
        } catch (\Exception $e) {
            \Log::error('Error getting file size: ' . $e->getMessage());
            return 'Unknown';
        }
    }
}
