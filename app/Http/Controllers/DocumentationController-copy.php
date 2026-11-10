<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentation;
use App\Models\Project;
use App\Models\Period;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class DocumentationController extends Controller
{
    public function upload(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'period_id' => 'required|exists:periods,id',
            'descriptions.*' => 'nullable|string|max:500',
            'titles.*' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $period = Period::findOrFail($request->period_id);
        
        // Pastikan periode milik project yang benar
        if ($period->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak sesuai dengan project'
            ], 403);
        }

        $uploadedFiles = [];
        
        try {
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $file) {
                    // Generate unique filename
                    $timestamp = Carbon::now()->format('YmdHis');
                    $randomString = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
                    $extension = $file->getClientOriginalExtension();
                    $filename = "doc_{$project->id}_{$period->id}_{$timestamp}_{$randomString}.{$extension}";
                    
                    // Store file
                    $path = $file->storeAs('documentation', $filename, 'public');
                    
                    // Get description and title for this photo
                    $description = $request->input("descriptions.{$index}", '');
                    $title = $request->input("titles.{$index}", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    
                    // Create documentation record
                    $documentation = Documentation::create([
                        'period_id' => $period->id,
                        'file_path' => $path,
                        'title' => $title,
                        'description' => $description,
                    ]);
                    
                    $uploadedFiles[] = [
                        'id' => $documentation->id,
                        'title' => $documentation->title,
                        'description' => $documentation->description,
                        'file_url' => Storage::url($path),
                        'created_at' => $documentation->created_at->format('d M Y H:i')
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' foto berhasil diupload',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            // Hapus file yang sudah terupload jika ada error
            foreach ($uploadedFiles as $uploaded) {
                if (isset($uploaded['file_path'])) {
                    Storage::disk('public')->delete($uploaded['file_path']);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function replacePhoto(Request $request, Project $project, Documentation $documentation)
    {
        // Pastikan dokumentasi milik project yang benar
        if ($documentation->period->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
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
            // Backup old file path
            $oldFilePath = $documentation->file_path;
            
            // Upload new file
            $file = $request->file('photo');
            $timestamp = Carbon::now()->format('YmdHis');
            $randomString = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
            $extension = $file->getClientOriginalExtension();
            $filename = "doc_{$project->id}_{$documentation->period_id}_{$timestamp}_{$randomString}.{$extension}";
            
            $newPath = $file->storeAs('documentation', $filename, 'public');
            
            // Update documentation record
            $documentation->update([
                'file_path' => $newPath,
                'title' => $request->input('title', $documentation->title),
                'description' => $request->input('description', $documentation->description)
            ]);
            
            // Delete old file
            if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }
            
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
                'file_path' => $documentation->file_path
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
            
            // Hapus record dari database terlebih dahulu
            $documentation->delete();
            
            // Kemudian hapus file dari storage jika ada
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                $deleted = Storage::disk('public')->delete($filePath);
                Log::info('File deletion result', [
                    'file_path' => $filePath,
                    'deleted' => $deleted
                ]);
            }
            
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
        // Pastikan dokumentasi milik project yang benar
        if ($documentation->period->project_id !== $project->id) {
            abort(404, 'Dokumentasi tidak ditemukan');
        }

        // Check if file exists
        if (!$documentation->file_path || !Storage::disk('public')->exists($documentation->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($documentation->file_path);
        $fileName = ($documentation->title ?: 'foto') . '.' . pathinfo($documentation->file_path, PATHINFO_EXTENSION);

        return Response::download($filePath, $fileName);
    }

    public function getPhotos(Request $request, Project $project)
    {
        $periodId = $request->input('period_id');
        
        if (!$periodId) {
            return response()->json([
                'success' => false,
                'message' => 'Period ID diperlukan'
            ], 400);
        }

        $period = Period::where('id', $periodId)
                       ->where('project_id', $project->id)
                       ->first();
                       
        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak ditemukan'
            ], 404);
        }

        $documentations = Documentation::where('period_id', $periodId)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        // Selalu return success dengan array kosong jika tidak ada dokumentasi
        $photos = $documentations->map(function($doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->title ?: 'Deskripsi Foto',
                'description' => $doc->description ?: 'Tidak ada deskripsi',
                'file_url' => $doc->file_url,
                'file_type' => $doc->file_type,
                'file_size' => $doc->file_size,
                'created_at' => $doc->created_at->format('d M Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'photos' => $photos,
            'count' => $photos->count()
        ]);
    }

    public function getPhoto(Project $project, Documentation $documentation)
    {
        // Pastikan dokumentasi milik project yang benar
        if ($documentation->period->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumentasi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $documentation->id,
                'title' => $documentation->title,
                'description' => $documentation->description,
                'file_url' => $documentation->file_url,
                'file_size' => $documentation->file_size,
                'created_at' => $documentation->created_at->format('d M Y H:i')
            ]
        ]);
    }
}