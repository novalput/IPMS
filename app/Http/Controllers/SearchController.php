<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Period;
use App\Models\Epc;
use App\Models\Engineering;
use App\Models\ProcurementMaterial;
use App\Models\ProcurementService;
use App\Models\Construction;
use App\Models\Activity;
use App\Models\Issue;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $filter = $request->input('filter', 'semua');
        
        $results = collect();
        
        if ($searchTerm) {
            $results = $this->performSearch($searchTerm, $startDate, $endDate, $filter);
        }
        
        return view('pages.project.search', compact('results', 'searchTerm', 'startDate', 'endDate', 'filter'));
    }
    
    private function performSearch($searchTerm, $startDate = null, $endDate = null, $filter = 'semua')
    {
        $results = collect();
        $searchPattern = "%{$searchTerm}%";
        
        // Build search parameters for URLs
        $searchParams = http_build_query(array_filter([
            'from_search' => '1',
            'search' => $searchTerm,
            'filter' => $filter,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]));
        
        // Helper function untuk filter tanggal
        $applyDateFilter = function($query, $dateColumn = 'created_at') use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween($dateColumn, [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } elseif ($startDate) {
                $query->where($dateColumn, '>=', Carbon::parse($startDate)->startOfDay());
            } elseif ($endDate) {
                $query->where($dateColumn, '<=', Carbon::parse($endDate)->endOfDay());
            }
            return $query;
        };
        
        // 1. SEARCH PROJECTS
        if ($filter === 'semua' || $filter === 'progress') {
            $projectQuery = Project::where(function($q) use ($searchPattern) {
                $q->where('name', 'LIKE', $searchPattern)
                  ->orWhere('description', 'LIKE', $searchPattern);
            });
            
            $applyDateFilter($projectQuery);
            
            foreach ($projectQuery->get() as $project) {
                $results->push([
                    'type' => 'Project',
                    'title' => $project->name,
                    'description' => $project->description,
                    'project_name' => $project->name,
                    'period_name' => 'Semua Periode',
                    'url' => route('project.description', $project->id) . ($searchParams ? '?' . $searchParams : ''),
                    'status' => $project->status,
                ]);
            }
        }
        
        // 2. SEARCH PERIODS
        if ($filter === 'semua' || $filter === 'progress') {
            $periodQuery = Period::with('project')->where(function($q) use ($searchPattern) {
                $q->where('name', 'LIKE', $searchPattern)
                  ->orWhere('status', 'LIKE', $searchPattern)
                  // PERBAIKAN: Tambah search untuk status display yang user friendly
                  ->orWhere(function($subQ) use ($searchPattern) {
                      // Search berdasarkan display status
                      if (stripos('draft', $searchPattern) !== false || stripos('saved', $searchPattern) !== false) {
                          $subQ->where('status', 'saved');
                      } elseif (stripos('submitted', $searchPattern) !== false || stripos('review', $searchPattern) !== false) {
                          $subQ->whereIn('status', [
                              'submitted',
                              'approved',
                              'rejected'
                          ]);
                      } elseif (stripos('approved', $searchPattern) !== false || stripos('selesai', $searchPattern) !== false) {
                          $subQ->where('status', 'approved');
                      }
                  });
            });
            
            if ($startDate || $endDate) {
                $periodQuery->where(function($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->where(function($subQ) use ($startDate, $endDate) {
                            $subQ->whereBetween('start_date', [$startDate, $endDate])
                                 ->orWhereBetween('end_date', [$startDate, $endDate])
                                 ->orWhere(function($dateQ) use ($startDate, $endDate) {
                                     $dateQ->where('start_date', '<=', $startDate)
                                           ->where('end_date', '>=', $endDate);
                                 });
                        });
                    } elseif ($startDate) {
                        $q->where('end_date', '>=', $startDate);
                    } elseif ($endDate) {
                        $q->where('start_date', '<=', $endDate);
                    }
                });
            }
            
            foreach ($periodQuery->get() as $period) {
                $periodParams = $searchParams . '&period_id=' . $period->id;
                $user = auth()->user();
                $displayStatus = $period->getDisplayStatusForUser($user);

                $results->push([
                    'type' => 'Periode',
                    'title' => $period->name,
                    'project_name' => $period->project->name,
                    'period_name' => $period->name,
                    'url' => route('project.description', ['project' => $period->project->id]) . '?' . $periodParams,
                ]);
            }
        }
        
        // 3. SEARCH EPC
        if ($filter === 'semua' || $filter === 'progress') {
            $epcQuery = Epc::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('description', 'LIKE', $searchPattern)
                  ->orWhere('status', 'LIKE', $searchPattern)
                  ->orWhere('weight_factor', 'LIKE', $searchPattern)
                  ->orWhere('cum_last_plan', 'LIKE', $searchPattern)
                  ->orWhere('cum_last_actual', 'LIKE', $searchPattern)
                  ->orWhere('cum_last_variance', 'LIKE', $searchPattern)
                  ->orWhere('this_plan', 'LIKE', $searchPattern)
                  ->orWhere('this_actual', 'LIKE', $searchPattern)
                  ->orWhere('cum_now_plan', 'LIKE', $searchPattern)
                  ->orWhere('cum_now_actual', 'LIKE', $searchPattern)
                  ->orWhere('cum_now_variance', 'LIKE', $searchPattern);
            });
            
            foreach ($epcQuery->get() as $epc) {
                if ($this->passesDateFilter($epc->period, $startDate, $endDate)) {
                    $epcParams = $searchParams . '&period_id=' . $epc->period->id;

                    $results->push([
                        'type' => 'EPC Progress',
                        'title' => 'EPC: ' . $epc->description,
                        'description' => "Weight Factor: {$epc->weight_factor}, Status: {$epc->status}",
                        'project_name' => $epc->period->project->name,
                        'period_name' => $epc->period->name,
                        'progress' => $epc->cum_now_actual,
                        'status' => $epc->status,
                        'url' => route('project.progress', ['project' => $epc->period->project->id]) . '?' . $epcParams,
                    ]);
                }
            }
        }
        
        // 4. SEARCH ENGINEERING
        if ($filter === 'semua' || $filter === 'progress') {
            $engineeringQuery = Engineering::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('kategori', 'LIKE', $searchPattern)
                  ->orWhere('subkategori', 'LIKE', $searchPattern)
                  ->orWhere('sub', 'LIKE', $searchPattern)
                  ->orWhere('doc_part1', 'LIKE', $searchPattern)
                  ->orWhere('doc_part2', 'LIKE', $searchPattern)
                  ->orWhere('doc_part3', 'LIKE', $searchPattern)
                  ->orWhere('doc_part4', 'LIKE', $searchPattern)
                  ->orWhere('doc_part5', 'LIKE', $searchPattern)
                  ->orWhere('doc_part6', 'LIKE', $searchPattern)
                  ->orWhere('document_name', 'LIKE', $searchPattern)
                  ->orWhere('approval_status', 'LIKE', $searchPattern);
            });
            
            foreach ($engineeringQuery->get() as $eng) {
                if ($this->passesDateFilter($eng->period, $startDate, $endDate)) {
                    $docNumber = collect([
                        $eng->doc_part1, $eng->doc_part2, $eng->doc_part3,
                        $eng->doc_part4, $eng->doc_part5, $eng->doc_part6
                    ])->filter()->implode('-');
                    
                    $engParams = $searchParams . '&period_id=' . $eng->period->id;
                    $results->push([
                        'type' => 'Engineering Document',
                        'title' => $eng->document_name ?: 'Engineering Document',
                        'description' => "Kategori: {$eng->kategori}, Nomor: {$docNumber}",
                        'project_name' => $eng->period->project->name,
                        'period_name' => $eng->period->name,
                        'approval_status' => $eng->approval_status,
                        'url' => route('project.progress', ['project' => $eng->period->project->id]) . '?' . $engParams,
                    ]);
                }
            }
        }
        
        // 5. SEARCH PROCUREMENT MATERIAL
        if ($filter === 'semua' || $filter === 'progress') {
            $materialQuery = ProcurementMaterial::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('material_name', 'LIKE', $searchPattern)
                  ->orWhere('po_issued', 'LIKE', $searchPattern)
                  ->orWhere('submit_tpg', 'LIKE', $searchPattern)
                  ->orWhere('approve_tpg', 'LIKE', $searchPattern)
                  ->orWhere('manufacture', 'LIKE', $searchPattern)
                  ->orWhere('fat', 'LIKE', $searchPattern)
                  ->orWhere('delivery', 'LIKE', $searchPattern)
                  ->orWhere('material_on_site', 'LIKE', $searchPattern);
            });
            
            foreach ($materialQuery->get() as $material) {
                if ($this->passesDateFilter($material->period, $startDate, $endDate)) {
                    $materialParams = $searchParams . '&period_id=' . $material->period->id;
                    $results->push([
                        'type' => 'Procurement Material',
                        'title' => 'Material: ' . $material->material_name,
                        'description' => "PO: {$material->po_issued}, Delivery: {$material->delivery}",
                        'project_name' => $material->period->project->name,
                        'period_name' => $material->period->name,
                        'url' => route('project.progress', ['project' => $material->period->project->id]) . '?' . $materialParams,
                    ]);
                }
            }
        }
        
        // 6. SEARCH PROCUREMENT SERVICE
        if ($filter === 'semua' || $filter === 'progress') {
            $serviceQuery = ProcurementService::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('service_name', 'LIKE', $searchPattern)
                  ->orWhere('po_issued', 'LIKE', $searchPattern)
                  ->orWhere('mobilization', 'LIKE', $searchPattern)
                  ->orWhere('field_work', 'LIKE', $searchPattern)
                  ->orWhere('reporting', 'LIKE', $searchPattern);
            });
            
            foreach ($serviceQuery->get() as $service) {
                if ($this->passesDateFilter($service->period, $startDate, $endDate)) {
                    $serviceParams = $searchParams . '&period_id=' . $service->period->id;
                    $results->push([
                        'type' => 'Procurement Service',
                        'title' => 'Service: ' . $service->service_name,
                        'description' => "PO: {$service->po_issued}, Field Work: {$service->field_work}",
                        'project_name' => $service->period->project->name,
                        'period_name' => $service->period->name,
                        'url' => route('project.progress', ['project' => $service->period->project->id]) . '?' . $serviceParams,
                    ]);
                }
            }
        }
        
        // 7. SEARCH CONSTRUCTION
        if ($filter === 'semua' || $filter === 'progress') {
            $constructionQuery = Construction::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('description', 'LIKE', $searchPattern)
                  ->orWhere('status', 'LIKE', $searchPattern)
                  ->orWhere('weight_factor', 'LIKE', $searchPattern)
                  ->orWhere('cum_last_plan', 'LIKE', $searchPattern)
                  ->orWhere('cum_last_actual', 'LIKE', $searchPattern)
                  ->orWhere('cum_last_variance', 'LIKE', $searchPattern)
                  ->orWhere('this_plan', 'LIKE', $searchPattern)
                  ->orWhere('this_actual', 'LIKE', $searchPattern)
                  ->orWhere('cum_now_plan', 'LIKE', $searchPattern)
                  ->orWhere('cum_now_actual', 'LIKE', $searchPattern)
                  ->orWhere('cum_now_variance', 'LIKE', $searchPattern);
            });
            
            foreach ($constructionQuery->get() as $construction) {
                if ($this->passesDateFilter($construction->period, $startDate, $endDate)) {
                    $constructionParams = $searchParams . '&period_id=' . $construction->period->id;
                    $results->push([
                        'type' => 'Construction Progress',
                        'title' => 'Construction: ' . $construction->description,
                        'description' => "Weight Factor: {$construction->weight_factor}, Status: {$construction->status}",
                        'project_name' => $construction->period->project->name,
                        'period_name' => $construction->period->name,
                        'progress' => $construction->cum_now_actual,
                        'status' => $construction->status,
                        'url' => route('project.progress', ['project' => $construction->period->project->id]) . '?' . $constructionParams,
                    ]);
                }
            }
        }
        
        // 8. SEARCH ACTIVITIES
        if ($filter === 'semua' || $filter === 'aktivitas') {
            $activityQuery = Activity::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('scope', 'LIKE', $searchPattern)
                  ->orWhere('location', 'LIKE', $searchPattern)
                  ->orWhere('achievement_this_week', 'LIKE', $searchPattern)
                  ->orWhere('plan_next_week', 'LIKE', $searchPattern)
                  ->orWhere('status', 'LIKE', $searchPattern);
            });
            
            foreach ($activityQuery->get() as $activity) {
                if ($this->passesDateFilter($activity->period, $startDate, $endDate)) {
                    $actParams = $searchParams . '&period_id=' . $activity->period->id;
                    $results->push([
                        'type' => 'Activity',
                        'title' => "Activity (Scope {$activity->scope}): {$activity->location}",
                        'description' => "Pencapaian: {$activity->achievement_this_week}",
                        'project_name' => $activity->period->project->name,
                        'period_name' => $activity->period->name,
                        'location' => $activity->location,
                        'progress' => $activity->progress,
                        'status' => $activity->status,
                        'url' => route('project.activities', ['project' => $activity->period->project->id]) . '?' . $actParams,
                    ]);
                }
            }
        }
        
        // 9. SEARCH ISSUES
        if ($filter === 'semua' || $filter === 'isu') {
            $issueQuery = Issue::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('name', 'LIKE', $searchPattern)
                  ->orWhere('location', 'LIKE', $searchPattern)
                  ->orWhere('equipment', 'LIKE', $searchPattern)
                  ->orWhere('description', 'LIKE', $searchPattern)
                  ->orWhere('status', 'LIKE', $searchPattern);
            });
            
            foreach ($issueQuery->get() as $issue) {
                if ($this->passesDateFilter($issue->period, $startDate, $endDate)) {
                    $issueParams = $searchParams . '&period_id=' . $issue->period->id;
                    $results->push([
                        'type' => 'Issue',
                        'title' => 'Issue: ' . $issue->name,
                        'description' => $issue->description,
                        'project_name' => $issue->period->project->name,
                        'period_name' => $issue->period->name,
                        'location' => $issue->location,
                        'equipment' => $issue->equipment,
                        'status' => $issue->status,
                        'url' => route('project.issues', ['project' => $issue->period->project->id]) . '?' . $issueParams,
                    ]);
                }
            }
        }
        
        // 10. SEARCH DOCUMENTATION
        if ($filter === 'semua' || $filter === 'dokumentasi') {
            $documentationQuery = Documentation::with('period.project')->where(function($q) use ($searchPattern) {
                $q->where('title', 'LIKE', $searchPattern)
                  ->orWhere('description', 'LIKE', $searchPattern);
            });
            
            foreach ($documentationQuery->get() as $documentation) {
                if ($this->passesDateFilter($documentation->period, $startDate, $endDate)) {
                    $docParams = $searchParams . '&period_id=' . $documentation->period->id;
                    $results->push([
                        'type' => 'Documentation',
                        'title' => 'Dokumentasi: ' . $documentation->title,
                        'description' => $documentation->description ?: 'Tidak ada deskripsi',
                        'project_name' => $documentation->period->project->name,
                        'period_name' => $documentation->period->name,
                        'file_path' => $documentation->file_path,
                        'url' => route('project.documentation', ['project' => $documentation->period->project->id]) . '?' . $docParams,
                    ]);
                }
            }
        }
        
        // Sort results by relevance (exact matches first, then partial matches)
        return $results->sortBy(function($item) use ($searchTerm) {
            $title = strtolower($item['title']);
            $searchLower = strtolower($searchTerm);
            
            if ($title === $searchLower) return 1; // Exact match
            if (str_contains($title, $searchLower)) return 2; // Contains search term
            return 3; // Other matches
        })->values();
    }
    
    private function passesDateFilter($period, $startDate, $endDate)
    {
        if (!$startDate && !$endDate) {
            return true;
        }
        
        $periodStart = $period->start_date ? Carbon::parse($period->start_date) : null;
        $periodEnd = $period->end_date ? Carbon::parse($period->end_date) : null;
        $filterStart = $startDate ? Carbon::parse($startDate) : null;
        $filterEnd = $endDate ? Carbon::parse($endDate) : null;
        
        // Jika periode tidak punya tanggal, fallback ke created_at
        if (!$periodStart && !$periodEnd) {
            $createdAt = Carbon::parse($period->created_at);
            if ($filterStart && $filterEnd) {
                return $createdAt->between($filterStart, $filterEnd);
            } elseif ($filterStart) {
                return $createdAt->gte($filterStart);
            } elseif ($filterEnd) {
                return $createdAt->lte($filterEnd);
            }
        }
        
        // Check if period overlaps with filter range
        if ($filterStart && $filterEnd) {
            if ($periodStart && $periodEnd) {
                return !($periodEnd->lt($filterStart) || $periodStart->gt($filterEnd));
            } elseif ($periodStart) {
                return $periodStart->lte($filterEnd);
            } elseif ($periodEnd) {
                return $periodEnd->gte($filterStart);
            }
        } elseif ($filterStart) {
            return $periodEnd ? $periodEnd->gte($filterStart) : true;
        } elseif ($filterEnd) {
            return $periodStart ? $periodStart->lte($filterEnd) : true;
        }
        
        return true;
    }
}