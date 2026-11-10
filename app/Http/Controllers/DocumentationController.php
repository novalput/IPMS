<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentation;
use App\Models\Project;
use App\Models\Period;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class DocumentationController extends Controller
{
    // Helper untuk mengubah DB Path (/storage/...) menjadi Storage Path (images/...)
    private function getStoragePath($dbPath)
    {
        // Hapus '/storage/' di depan agar bisa dicek oleh Storage::disk('public')
        return str_replace('/storage/', '', $dbPath);
    }

    public function upload(Project $project, Request $request)
    {
        try {
            $request->validate([
                'period_id' => 'required|exists:periods,id',
                'photos' => 'required|array|min:1',
                'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);

            $uploadedFiles = [];
            $photos = $request->file('photos');
            $titles = $request->input('titles', []);
            $descriptions = $request->input('descriptions', []);

            foreach ($photos as $index => $photo) {
                // 1. Generate nama file unik
                $filename = time() . '_' . $index . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                
                // 2. Simpan fisik file ke: storage/app/public/images/documentation
                // Hasil $path adalah: "images/documentation/namafile.png"
                $path = $photo->storeAs('images/documentation', $filename, 'public');
                
                // 3. Buat URL untuk database agar sesuai format Anda (/storage/...)
                $dbPath = '/storage/' . $path;

                $documentation = Documentation::create([
                    'period_id' => $request->period_id,
                    'title' => $titles[$index] ?? $photo->getClientOriginalName(),
                    'description' => $descriptions[$index] ?? '',
                    'file_path' => $dbPath, // Konsisten simpan: /storage/images/documentation/...
                ]);

                // Hitung ukuran file untuk response langsung
                $size = $photo->getSize();
                
                $uploadedFiles[] = [
                    'id' => $documentation->id,
                    'title' => $documentation->title,
                    'description' => $documentation->description,
                    'file_url' => asset($dbPath), // Generate full URL http://domain.com/storage/...
                    'file_size' => round($size / 1024, 2) . ' KB',
                    'created_at' => $documentation->created_at->format('d M Y H:i')
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diunggah',
                'data' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Upload Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function replacePhoto(Request $request, Project $project, Documentation $documentation)
    {
        if ($documentation->period->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak ditemukan'
            ], 404);
        }

        $validator = \Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldFilePath = $documentation->file_path;
            $file = $request->file('photo');

            $timestamp = \Carbon\Carbon::now()->format('YmdHis');
            $randomString = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
            $extension = $file->getClientOriginalExtension();
            $filename = "doc_{$project->id}_{$documentation->period_id}_{$timestamp}_{$randomString}.{$extension}";

            // SIMPAN PASTI KE images/documentation DI DISK PUBLIC
            $newPath = $file->storeAs('images/documentation', $filename, 'public');

            $documentation->update([
                'file_path' => $newPath,
                'title' => $request->input('title', $documentation->title),
                'description' => $request->input('description', $documentation->description)
            ]);
            
            // Hapus file lama (jika ada)
            if ($oldFilePath && \Storage::disk('public')->exists($oldFilePath)) {
                \Storage::disk('public')->delete($oldFilePath);
            }

            // $doc = Documentation::findOrFail($documentation->id);
            // $doc->load('period');
            // if ($doc->period) {
            //     $this->touchPeriodAudit($doc->period);
            // }
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diganti',
                'documentation' => [
                    'id' => $documentation->id,
                    'title' => $documentation->title,
                    'description' => $documentation->description,
                    'file_url' => $documentation->file_url
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengganti foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, Project $project, Documentation $documentation)
    {
    try {
        // Log untuk debugging
        Log::info('Delete photo request', [
            'project_id' => $project->id,
            'documentation_id' => $documentation->id,
            'file_path' => $documentation->file_path,
            'method' => $request->method()
        ]);

        // Pastikan dokumentasi milik project yang benar
        if ($documentation->period->project_id !== $project->id) {
            Log::warning('Documentation does not belong to project', [
                'documentation_period_project_id' => $documentation->period->project_id,
                'requested_project_id' => $project->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak ditemukan'
            ], 404);
        }

        // Simpan path file untuk dihapus
        $filePath = $documentation->file_path;
        $period = $documentation->period; // Simpan referensi period
              
        // Kemudian hapus file dari storage jika ada
        if ($filePath) {
            // Untuk production
            if (app()->environment('production')) {
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
                    $deleted = unlink($fullPath);
                    Log::info('File deletion result', [
                        'file_path' => $fullPath,
                        'deleted' => $deleted
                    ]);
                }
            } else {
                // Untuk local development
                if (Storage::disk('public')->exists($filePath)) {
                    $deleted = Storage::disk('public')->delete($filePath);
                    Log::info('File deletion result', [
                        'file_path' => $filePath,
                        'deleted' => $deleted
                    ]);
                }
            }
        }
    

    // Hapus record dari database terlebih dahulu
        $documentation->delete();
    // Touch audit period
    //    if ($period) {
    //        $this->touchPeriodAudit($period);
    //    }
        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error deleting documentation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus foto: ' . $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, Project $project, Documentation $documentation)
    {
        // Pastikan dokumentasi milik project yang benar
        if ($documentation->period->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak ditemukan'
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $documentation->update([
                'title' => $request->input('title', $documentation->title),
                'description' => $request->input('description', $documentation->description)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Deskripsi foto berhasil diupdate',
                'documentation' => [
                    'id' => $documentation->id,
                    'title' => $documentation->title,
                    'description' => $documentation->description,
                    'file_url' => $documentation->file_url
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate deskripsi: ' . $e->getMessage()
            ], 500);
        }
}

    public function download(Project $project, Documentation $documentation)
    {
        \Log::info('Download attempt:', [
            'project_id' => $project->id,
            'doc_id' => $documentation->id,
            'file_path' => $documentation->file_path,
            'full_path' => Storage::disk('public')->path($documentation->file_path)
        ]);

        if ($documentation->period->project_id !== $project->id) {
            abort(404, 'Dokumentasi tidak ditemukan');
        }

        if (!$documentation->file_path || !Storage::disk('public')->exists($documentation->file_path)) {
            \Log::error('File not found for download', ['path' => $documentation->file_path]);
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($documentation->file_path);
        $fileName = ($documentation->title ?: 'foto') . '.' . pathinfo($documentation->file_path, PATHINFO_EXTENSION);

        return Response::download($filePath, $fileName);
    }

    // Untuk List Foto (Grid)
    public function getPhotos(Project $project, Request $request)
    {
        try {
            $periodId = $request->query('period_id');
            $query = Documentation::query();

            if ($periodId) {
                $query->where('period_id', $periodId);
            } else {
                $query->whereHas('period', function($q) use ($project) {
                    $q->where('project_id', $project->id);
                });
            }
            
            $docs = $query->latest()->get();
            $docData = [];

            foreach ($docs as $doc) {
                // Ambil path dari DB (/storage/images/...)
                $dbPath = $doc->file_path;
                
                // Konversi ke relative path (images/...) untuk cek fisik
                $storagePath = $this->getStoragePath($dbPath);
                
                $fileSize = 'File not found';
                // Default URL pakai asset() agar domainnya lengkap
                $finalUrl = asset($dbPath); 

                // Cek apakah file fisik ada
                if (Storage::disk('public')->exists($storagePath)) {
                    try {
                        $sizeBytes = Storage::disk('public')->size($storagePath);
                        $fileSize = round($sizeBytes / 1024, 2) . ' KB';
                    } catch (\Exception $e) {
                        $fileSize = 'Unknown';
                    }
                } else {
                    // Opsional: jika file hilang fisik tapi ada di DB, bisa kasih gambar placeholder
                    // $finalUrl = asset('images/placeholder.png'); 
                }

                $docData[] = [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'description' => $doc->description,
                    'file_url' => $finalUrl, // Ini yang dipakai <img src="...">
                    'file_size' => $fileSize,
                    'created_at' => $doc->created_at->format('d M Y H:i'),
                ];
            }
            
            return response()->json(['success' => true, 'photos' => $docData]); // Perhatikan key 'photos'
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getPhoto(Project $project, Documentation $documentation)
    {
        // DEBUGGING: Log semua data yang masuk
        \Log::info('=== DEBUG getPhoto START ===');
        \Log::info('Project ID: ' . $project->id);
        \Log::info('Project Name: ' . $project->name ?? 'NULL');
        \Log::info('Documentation ID: ' . $documentation->id);
        \Log::info('Documentation Title: ' . $documentation->title ?? 'NULL');
        \Log::info('Documentation Period ID: ' . $documentation->period_id ?? 'NULL');
        
        try {
            // DEBUGGING: Cek apakah documentation punya period
            if (!$documentation->period_id) {
                \Log::error('Documentation tidak memiliki period_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Data dokumentasi tidak valid - tidak memiliki periode'
                ], 400);
            }
            
            // DEBUGGING: Manual query untuk mendapatkan period
            $period = \App\Models\Period::find($documentation->period_id);
            if (!$period) {
                \Log::error('Period tidak ditemukan untuk period_id: ' . $documentation->period_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }
            
            \Log::info('Period found: ' . $period->id . ' - ' . $period->name);
            \Log::info('Period Project ID: ' . $period->project_id);
            
            // DEBUGGING: Cek project_id
            if ($period->project_id != $project->id) {
                \Log::error('Period tidak milik project ini. Period project_id: ' . $period->project_id . ', Request project_id: ' . $project->id);
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumentasi tidak ditemukan untuk proyek ini'
                ], 404);
            }
            
            // DEBUGGING: Cek file path
            \Log::info('Documentation file_path: ' . ($documentation->file_path ?? 'NULL'));
            
            // DEBUGGING: Test file_url accessor
            try {
                $fileUrl = $documentation->file_url;
                \Log::info('File URL generated: ' . ($fileUrl ?? 'NULL'));
            } catch (\Exception $e) {
                \Log::error('Error generating file_url: ' . $e->getMessage());
                $fileUrl = null;
            }
            
            // DEBUGGING: Test file_size_formatted accessor  
            try {
                $fileSize = $documentation->file_size_formatted;
                \Log::info('File size formatted: ' . ($fileSize ?? 'NULL'));
            } catch (\Exception $e) {
                \Log::error('Error getting file_size_formatted: ' . $e->getMessage());
                $fileSize = 'Unknown';
            }
            
            // DEBUGGING: Test created_at
            try {
                $createdAt = $documentation->created_at ? $documentation->created_at->format('d M Y H:i') : 'Unknown';
                \Log::info('Created at formatted: ' . $createdAt);
            } catch (\Exception $e) {
                \Log::error('Error formatting created_at: ' . $e->getMessage());
                $createdAt = 'Unknown';
            }
            
            // Build response data step by step
            $photoData = [];
            
            try {
                $photoData['id'] = $documentation->id;
                $photoData['title'] = $documentation->title ?? 'Tanpa Judul';
                $photoData['description'] = $documentation->description ?? '';
                $photoData['file_url'] = $fileUrl;
                $photoData['file_size'] = $fileSize;
                $photoData['created_at'] = $createdAt;
                
                \Log::info('Photo data built successfully');
                
            } catch (\Exception $e) {
                \Log::error('Error building photo data: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error saat membangun data foto'
                ], 500);
            }
    
            \Log::info('=== DEBUG getPhoto SUCCESS ===');
            \Log::info('Final photo data: ', $photoData);
    
            return response()->json([
                'success' => true,
                'photo' => $photoData
            ]);
    
        } catch (\Exception $e) {
            \Log::error('=== DEBUG getPhoto ERROR ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile());
            \Log::error('Error line: ' . $e->getLine());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    // Untuk Detail Modal (Single Photo)
    public function getPhotoAlternative(Request $request, $projectId, $documentationId)
    {
        try {
            $documentation = Documentation::find($documentationId);
            
            if (!$documentation) {
                return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan'], 404);
            }

            $dbPath = $documentation->file_path;
            $storagePath = $this->getStoragePath($dbPath);
            
            $fileSize = 'Unknown';
            if (Storage::disk('public')->exists($storagePath)) {
                $sizeBytes = Storage::disk('public')->size($storagePath);
                $fileSize = round($sizeBytes / 1024, 2) . ' KB';
            }

            return response()->json([
                'success' => true,
                'photo' => [
                    'id' => $documentation->id,
                    'title' => $documentation->title ?? 'Tanpa Judul',
                    'description' => $documentation->description ?? '',
                    'file_url' => asset($dbPath), // Pastikan pakai asset()
                    'file_size' => $fileSize,
                    'created_at' => $documentation->created_at->format('d M Y H:i'),
                    'file_exists' => Storage::disk('public')->exists($storagePath)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Replace Photo
    public function replacePhotoAlternative(Request $request, $projectId, $documentationId)
    {
        try {
            $documentation = Documentation::find($documentationId);
            if (!$documentation) return response()->json(['success'=>false], 404);

            $request->validate(['photo' => 'required|image|max:10240']);

            // 1. Hapus file lama
            $oldStoragePath = $this->getStoragePath($documentation->file_path);
            if (Storage::disk('public')->exists($oldStoragePath)) {
                Storage::disk('public')->delete($oldStoragePath);
            }

            // 2. Upload file baru
            $file = $request->file('photo');
            $filename = time() . '_rep_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/documentation', $filename, 'public');
            $dbPath = '/storage/' . $path;

            // 3. Update DB
            $documentation->update([
                'file_path' => $dbPath,
                'title' => $request->input('title', $documentation->title),
                'description' => $request->input('description', $documentation->description)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diganti',
                'documentation' => [ // Return data format consistent
                    'file_url' => asset($dbPath)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    // Delete
    public function deleteAlternative(Request $request, $projectId, $documentationId)
    {
        try {
            $documentation = Documentation::find($documentationId);
            if (!$documentation) return response()->json(['success'=>false], 404);

            // Hapus file fisik
            $storagePath = $this->getStoragePath($documentation->file_path);
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            $documentation->delete();

            return response()->json(['success' => true, 'message' => 'Foto dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    // Download
    public function downloadAlternative(Request $request, $projectId, $documentationId)
    {
        $documentation = Documentation::find($documentationId);
        if (!$documentation) abort(404);

        $storagePath = $this->getStoragePath($documentation->file_path);

        if (!Storage::disk('public')->exists($storagePath)) {
            abort(404, 'File fisik tidak ditemukan');
        }

        return Storage::disk('public')->download($storagePath, $documentation->title . '.' . pathinfo($storagePath, PATHINFO_EXTENSION));
    }

public function updateAlternative(Request $request, $projectId, $documentationId)
    {
    try {
        // Manual query
        $project = \App\Models\Project::find($projectId);
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Proyek tidak ditemukan'
            ], 404);
        }
        
        $documentation = \App\Models\Documentation::find($documentationId);
        if (!$documentation) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak ditemukan'
            ], 404);
        }
        
        // Load period relationship
        $documentation->load('period');
        
        if (!$documentation->period || $documentation->period->project_id != $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak milik proyek ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $documentation->update([
            'title' => $request->input('title', $documentation->title),
            'description' => $request->input('description', $documentation->description)
        ]);
        
        // $doc = Documentation::findOrFail($documentation->id);
        // $doc->load('period');
        // if ($doc->period) {
        //     $this->touchPeriodAudit($doc->period);
        // }
        
        return response()->json([
            'success' => true,
            'message' => 'Deskripsi foto berhasil diupdate',
            'documentation' => [
                'id' => $documentation->id,
                'title' => $documentation->title,
                'description' => $documentation->description,
                'file_url' => $documentation->file_url
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate deskripsi: ' . $e->getMessage()
        ], 500);
    }
}
public function debugRoute(Request $request, Project $project, Documentation $documentation)
{
    \Log::info('Route Debug Info:', [
        'method' => $request->method(),
        'url' => $request->url(),
        'route_name' => $request->route()->getName(),
        'project_id' => $project->id,
        'documentation_id' => $documentation->id,
        'all_input' => $request->all(),
        'headers' => $request->headers->all()
    ]);
    
    return response()->json([
        'success' => true,
        'debug_info' => [
            'method' => $request->method(),
            'project_id' => $project->id,
            'documentation_id' => $documentation->id,
            'input' => $request->all()
        ]
    ]);
}
    
}