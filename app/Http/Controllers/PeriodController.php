<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\Project;
use App\Models\Tracking;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function create(Project $project)
    {
        return view('pages.period.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'week' => 'nullable|integer',
            'month' => 'nullable|integer',
        ]);
        
        $period = Period::create([
            'project_id' => $project->id,
            'name' => $request->name,
            'week' => $request->week,
            'month' => $request->month,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        
        return redirect()->route('project.show', $project->id)->with('success', 'Periode berhasil ditambahkan!');
    }

    public function edit(Period $period)
    {
        return view('pages.period.edit', compact('period'));
    }

    public function update(Request $request, Period $period)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'week' => 'nullable|integer',
            'month' => 'nullable|integer',
        ]);
        
        $period->update([
            'name' => $request->name,
            'week' => $request->week,
            'month' => $request->month,
            'updated_by' => auth()->id(),
        ]);
        
        return redirect()->route('project.show', $period->project_id)->with('success', 'Periode berhasil diperbarui!');
    }

    public function destroy(Period $period)
    {
        $user = auth()->user();
        
        // Hanya admin yang bisa delete periode
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk menghapus periode.');
        }

        $projectName = $period->project->name;
        $periodName = $period->name;
        
        // Delete semua data terkait periode
        $period->epc()->delete();
        $period->engineerings()->delete(); 
        $period->procurementMaterials()->delete();
        $period->procurementServices()->delete();
        $period->constructions()->delete();
        $period->activities()->delete();
        $period->issues()->delete();
        $period->documentations()->delete();
        
        // Delete tracking records
        Tracking::where('period_id', $period->id)->delete();
        
        // Delete project progress records
        \App\Models\ProjectProgress::where('period_id', $period->id)->delete();
        
        // Delete periode
        $period->delete();
        
        return redirect()->route('dashboard')->with('success', "Periode '{$periodName}' dari proyek '{$projectName}' berhasil dihapus.");
    }

    // Untuk dropdown periode (AJAX)
    public function dropdownPeriods($project_id)
    {
        $periods = Period::where('project_id', $project_id)
        ->select('id', 'name', 'start_date', 'end_date')
        ->get()
        ->map(function($period) {
            return [
                'id' => $period->id,
                'name' => $period->name,
                'start_date' => $period->start_date ? $period->start_date->format('Y-m-d') : null,
                'end_date' => $period->end_date ? $period->end_date->format('Y-m-d') : null,
            ];
        });
        return response()->json($periods);
    }

    public function copyTemplate($period_id)
    {
        try {
            // Load period dengan semua relasi data
            $period = Period::with([
                'epc',
                'engineerings', 
                'procurementMaterials',
                'procurementServices',
                'constructions',
                'activities',
                'issues'
            ])->findOrFail($period_id);

            // Format data lengkap untuk autofill form
            $templateData = [
                'success' => true,
                'period' => [
                    'id' => $period->id,
                    'name' => $period->name,
                    'start_date' => $period->start_date ? $period->start_date->format('Y-m-d') : null,
                    'end_date' => $period->end_date ? $period->end_date->format('Y-m-d') : null,
                ],
                'epc' => $period->epc->map(function($item) {
                    return [
                        'description' => $item->description,
                        'weight_factor' => $item->weight_factor,
                        'cum_last_plan' => $item->cum_last_plan,
                        'cum_last_actual' => $item->cum_last_actual,
                        'cum_last_variance' => $item->cum_last_variance,
                        'this_plan' => $item->this_plan,
                        'this_actual' => $item->this_actual,
                        'cum_now_plan' => $item->cum_now_plan,
                        'cum_now_actual' => $item->cum_now_actual,
                        'cum_now_variance' => $item->cum_now_variance,
                        'status' => $item->status,
                        'is_total' => $item->is_total,
                    ];
                })->values()->toArray(),
                
                // UPDATE: Engineering dengan struktur hierarkis untuk generate kategori/subkategori/sub
                'engineering' => $this->formatEngineeringHierarchy($period->engineerings),
                
                'materials' => $period->procurementMaterials->map(function($item) {
                    return [
                        'material_name' => $item->material_name,
                        'po_issued' => $item->po_issued,
                        'submit_tpg' => $item->submit_tpg,
                        'approve_tpg' => $item->approve_tpg,
                        'manufacture' => $item->manufacture,
                        'fat' => $item->fat,
                        'delivery' => $item->delivery,
                        'material_on_site' => $item->material_on_site
                    ];
                })->values()->toArray(),
                
                'services' => $period->procurementServices->map(function($item) {
                    return [
                        'service_name' => $item->service_name,
                        'po_issued' => $item->po_issued,
                        'mobilization' => $item->mobilization,
                        'field_work' => $item->field_work,
                        'reporting' => $item->reporting
                    ];
                })->values()->toArray(),
                
                'constructions' => $period->constructions->map(function($item) {
                    return [
                        'description' => $item->description,
                        'weight_factor' => $item->weight_factor,
                        'cum_last_plan' => $item->cum_last_plan,
                        'cum_last_actual' => $item->cum_last_actual,
                        'cum_last_variance' => $item->cum_last_variance,
                        'this_plan' => $item->this_plan,
                        'this_actual' => $item->this_actual,
                        'cum_now_plan' => $item->cum_now_plan,
                        'cum_now_actual' => $item->cum_now_actual,
                        'cum_now_variance' => $item->cum_now_variance,
                        'status' => $item->status,
                        'is_total' => $item->is_total,
                    ];
                })->values()->toArray(),
                
                'activities' => $period->activities->groupBy('scope')->map(function($scopeActivities) {
                    return $scopeActivities->map(function($item) {
                        return [
                            'location' => $item->location,
                            'achievement_this_week' => $item->achievement_this_week,
                            'progress' => $item->progress,
                            'plan_next_week' => $item->plan_next_week,
                            'status' => $item->status
                        ];
                    })->values();
                })->toArray(),
                
                'issues' => $period->issues->map(function($item) {
                    return [
                        'name' => $item->name,
                        'location' => $item->location,
                        'equipment' => $item->equipment,
                        'description' => $item->description,
                        'status' => $item->status
                    ];
                })->values()->toArray()
            ];

            return response()->json($templateData);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat template periode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format engineering data dengan struktur hierarkis
     */
    private function formatEngineeringHierarchy($engineerings)
{
    $hierarchy = [];
    $globalDocIndex = 1;

    // Group by kategori, lalu urutkan ASCENDING
    $grouped = $engineerings->groupBy('kategori')->sortKeys();

    foreach ($grouped as $kategori => $kategoriItems) {
        $kategoriData = [
            'type' => 'kategori',
            'name' => $kategori,
            'subkategories' => []
        ];

        // Group by subkategori, lalu urutkan ASCENDING
        $subGroups = $kategoriItems->groupBy('subkategori')->sortKeys();

        foreach ($subGroups as $subkategori => $subkategoriItems) {
            $subkategoriData = [
                'type' => 'subkategori',
                'name' => $subkategori,
                'subs' => []
            ];

            // Group by sub, lalu urutkan ASCENDING
            $subSubGroups = $subkategoriItems->groupBy('sub')->sortKeys();

            foreach ($subSubGroups as $sub => $subItems) {
                $subData = [
                    'type' => 'sub',
                    'name' => $sub,
                    'documents' => []
                ];

                // Urutkan dokumen berdasarkan document_name atau id (optional)
                $sortedDocs = $subItems->sortBy('document_name')->values();

                foreach ($sortedDocs as $doc) {
                    $subData['documents'][] = [
                        'type' => 'document',
                        'index' => $globalDocIndex++,
                        'kategori' => $kategori,
                        'subkategori' => $subkategori,
                        'sub' => $sub,
                        'doc_part1' => $doc->doc_part1,
                        'doc_part2' => $doc->doc_part2,
                        'doc_part3' => $doc->doc_part3,
                        'doc_part4' => $doc->doc_part4,
                        'doc_part5' => $doc->doc_part5,
                        'doc_part6' => $doc->doc_part6,
                        'document_name' => $doc->document_name,
                        'approval_status' => $doc->approval_status
                    ];
                }

                $subkategoriData['subs'][] = $subData;
            }

            $kategoriData['subkategories'][] = $subkategoriData;
        }

        $hierarchy[] = $kategoriData;
    }

    return $hierarchy;
}

    public function cancelSubmit(Period $period)
    {
        $user = auth()->user();

        // Hanya Admin yang boleh cancel/reset
        if (! $user || ! $user->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin.');
        }

        // Reset status period
        $period->update([
            'status' => 'saved'
        ]);

        // Reset tracking
        \App\Models\Tracking::where('period_id', $period->id)->delete();

        return redirect()->back()->with('success', 'Status berhasil di-reset ke Draft.');
    }

}