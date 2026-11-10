<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Period;
use Illuminate\Http\Request;

class CompletedPeriodController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // 1. Query Project yang memiliki periode COMPLETED
        $query = Project::with(['periods' => function($q) {
            $q->where('is_completed', true) // Hanya ambil periode yang selesai
              ->orderBy('end_date', 'desc')
              ->with(['createdBy', 'updatedBy', 'projectProgress']);
        }])
        ->whereHas('periods', function($q) {
            $q->where('is_completed', true);
        });

        // 2. Filter Role User (Hanya lihat proyek yang dia kerjakan periodenya)
        if (!$user->isAdmin()) {
            $query->whereHas('periods', function($q) use ($user) {
                $q->where('is_completed', true)
                  ->where(function($inner) use ($user) {
                      $inner->where('created_by', $user->id)
                            ->orWhere('updated_by', $user->id);
                  });
            });
        }
        
        // Search Filter (Optional, samakan logic dengan NewProjectController jika perlu)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $projects = $query->get();

        // 3. Hitung Progress Total untuk Tampilan Bar (Pasti 100% kalau masuk sini, tapi tetap kita hitung)
        $projects->each(function($project) {
            $total = 0;
            foreach($project->periods as $period) {
                if($period->projectProgress) {
                    $total += ($period->projectProgress->progress_percentage * $period->projectProgress->weight);
                }
            }
            $project->total_accumulated_progress = min($total, 100);
        });
        
        return view('pages.completed-periods.index', compact('projects'));
    }
    
    public function reactivate(Period $period)
    {
        // Hanya admin yang bisa reactivate
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Tidak memiliki izin untuk mengaktifkan kembali periode.');
        }
        
        $period->update([
            'is_completed' => false,
            'completed_at' => null
        ]);
        
        return redirect()->back()->with('success', "Periode {$period->name} berhasil diaktifkan kembali.");
    }
}