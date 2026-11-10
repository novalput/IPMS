<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Period;
use App\Models\ProjectProgress;
use Illuminate\Http\Request;

class NewProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Ambil Project beserta Periodenya
        $query = Project::with(['periods' => function($q) {
            $q->active() // Scope active (is_completed = false)
              ->orderBy('start_date', 'desc')
              ->with(['createdBy', 'updatedBy', 'projectProgress']);
        }]);

        // Filter Role User
        if ($user->isUser()) {
            $query->whereHas('periods', function($q) use ($user) {
                $q->active()->where('created_by', $user->id);
            });
        }
        
        // Apply Search Filters (Method applyFilters anda tetap dipakai)
        $this->applyFilters($query, $request);

        $projects = $query->get();

        // Filter Project yang Periodenya Kosong (misal completed semua)
        $projects = $projects->filter(function($project) {
            return $project->periods->count() > 0;
        });

        // Hitung Total Progress per Project untuk Bar Accordion
        $projects->each(function($project) {
            $total = 0;
            foreach($project->periods as $period) {
                if($period->projectProgress) {
                    $total += ($period->projectProgress->progress_percentage * $period->projectProgress->weight);
                }
            }
            $project->total_accumulated_progress = min($total, 100);
        });

        return view('pages.project.new_project', compact('projects'));
    }

    private function getProgressChartData($project, $currentPeriod)
    {
        if (!$currentPeriod) {
            return [
                'labels' => [],
                'actual' => [],
                'count' => 0
            ];
        }

        // 1. Cari data progress berdasarkan period_id (HAPUS project_id)
        $progress = ProjectProgress::where('period_id', $currentPeriod->id)->first();

        $labels = [];
        $actual = [];

        if ($progress) {
            $labels[] = 'Current Progress';
            
            // 2. HITUNG MANUAL DI SINI (Runtime Calculation)
            // Rumus: (Progress % * Bobot)
            $calculatedValue = floatval($progress->progress_percentage) * floatval($progress->weight);
            
            $actual[] = $calculatedValue; // Masukkan hasil hitungan ke array
        } else {
            // Fallback jika belum ada data progress
            $labels[] = 'Current Progress';
            $actual[] = 0;
        }

        return [
            'labels' => $labels,
            'planned' => [], 
            'actual' => $actual,
            'count' => count($actual)
        ];
    }

    private function applyFilters($query, Request $request)
    {
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('periods', function($p) use ($search) {
                      $p->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->start_date;
            $end = $request->end_date;
            
            $query->where(function($q) use ($start, $end) {
                if ($start && $end) {
                    $q->whereBetween('created_at', [
                        \Carbon\Carbon::parse($start)->startOfDay(),
                        \Carbon\Carbon::parse($end)->endOfDay()
                    ]);
                } elseif ($start) {
                    $q->where('created_at', '>=', \Carbon\Carbon::parse($start)->startOfDay());
                } elseif ($end) {
                    $q->where('created_at', '<=', \Carbon\Carbon::parse($end)->endOfDay());
                }
            })->orWhereHas('periods', function($p) use ($start, $end) {
                if ($start && $end) {
                    $p->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end]);
                } elseif ($start) {
                    $p->where('end_date', '>=', $start);
                } elseif ($end) {
                    $p->where('start_date', '<=', $end);
                }
            });
        }
    }
}