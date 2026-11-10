<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Period;
use App\Models\ProjectProgress;
use App\Models\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // 1. STATISTIK CARDS
        $statistics = $this->getStatistics($user);
        
        // 2. PROGRESS PROYEK (Top 5 atau semua yang aktif)
        $projectProgress = $this->getProjectProgress($user);
        
        // 3. AKTIVITAS TERBARU
        $recentActivities = $this->getRecentActivities($user);
        
        // 4. NOTIFIKASI PENTING
        $importantNotifications = $this->getImportantNotifications($user);
        
        return view('pages.general.dashboard.dashboard', compact(
            'statistics',
            'projectProgress',
            'recentActivities',
            'importantNotifications'
        ));
    }

    private function getStatistics($user)
    {
        $query = Project::query();
        
        // Filter berdasarkan role
        if ($user->isUser()) {
            $query->whereHas('periods', function($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }
        
        $totalProjects = $query->count();
        
        // Proyek Aktif = punya periode yang belum completed
        $activeProjects = (clone $query)->whereHas('periods', function($q) {
            $q->where('is_completed', false);
        })->count();
        
        // Proyek Selesai = semua periode sudah completed
        $completedProjects = (clone $query)->whereDoesntHave('periods', function($q) {
            $q->where('is_completed', false);
        })->whereHas('periods')->count();
        
        return [
            'total' => $totalProjects,
            'active' => $activeProjects,
            'completed' => $completedProjects,
        ];
    }

    private function getProjectProgress($user)
    {
        // Ambil hari ini (start of day) untuk perbandingan
        $today = now()->startOfDay();

        // Ambil semua project dengan periode aktif DAN deadline masa depan/hari ini
        $query = Project::with(['periods' => function($q) use ($user, $today) {
            $q->where('is_completed', false)
              ->whereDate('end_date', '>=', $today) // [UBAH DISINI] Filter hanya deadline >= hari ini
              ->with(['projectProgress', 'createdBy', 'updatedBy'])
              ->orderBy('end_date', 'asc'); // Ubah sort jadi deadline terdekat dulu
            
            // Filter untuk user biasa
            if (auth()->user()->isUser()) {
                $q->where('created_by', auth()->id());
            }
        }]);

        // Filter Project Induk agar hanya muncul jika punya periode yang memenuhi syarat di atas
        $query->whereHas('periods', function($q) use ($user, $today) {
            $q->where('is_completed', false)
              ->whereDate('end_date', '>=', $today); // [UBAH DISINI] Filter Project
            
            if ($user->isUser()) {
                $q->where('created_by', $user->id);
            }
        });

        $projects = $query->get();
        
        $progressData = [];
        
        foreach ($projects as $project) {
            foreach ($project->periods as $period) {
                // Skip jika somehow masih ada tanggal masa lalu lolos (double check)
                if ($period->end_date < $today) continue; 

                // Hitung total progress (Logic lama tetap dipakai)
                $allPeriods = $project->periods;
                $totalProgress = $allPeriods->sum(function($p) {
                    return $p->projectProgress ? $p->projectProgress->calculated_progress : 0;
                });
                
                $progressData[] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'period_id' => $period->id,
                    'period_name' => $period->name,
                    'total_progress' => min($totalProgress, 100),
                    'period_progress' => $period->projectProgress ? $period->projectProgress->calculated_progress : 0,
                    'deadline' => $period->end_date ? $period->end_date->format('d M Y') : '-',
                    'deadline_raw' => $period->end_date, // Carbon object
                    'status' => $period->status,
                ];
            }
        }
        
        // Sort by deadline terdekat (asc) agar yang urgent muncul paling atas
        usort($progressData, function($a, $b) {
            return $a['deadline_raw'] <=> $b['deadline_raw'];
        });
        
        return array_slice($progressData, 0, 10);
    }

    private function getRecentActivities($user)
    {
        // Aktivitas berupa: create/update period, submit, approve, reject
        $activities = [];
        
        // Dari tracking table (submit/approve/reject)
        $trackingQuery = Tracking::with('period.project')
            ->orderBy('created_at', 'desc')
            ->limit(5);
        
        if ($user->isUser()) {
            $trackingQuery->whereHas('period', function($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }
        
        $trackings = $trackingQuery->get();
        
        foreach ($trackings as $tracking) {
            $activities[] = [
                'type' => $tracking->status,
                'title' => $this->getActivityTitle($tracking->status),
                'description' => "{$tracking->period->project->name} - {$tracking->period->name}",
                'user' => $tracking->sender,
                'timestamp' => $tracking->created_at,
                'icon' => $this->getActivityIcon($tracking->status),
                'color' => $this->getActivityColor($tracking->status),
            ];
        }
        
        // Dari period updates
        $periodQuery = Period::with(['project', 'updatedBy'])
            ->orderBy('updated_at', 'desc')
            ->limit(5);
        
        if ($user->isUser()) {
            $periodQuery->where('created_by', $user->id);
        }
        
        $periods = $periodQuery->get();
        
        foreach ($periods as $period) {
            $activities[] = [
                'type' => 'update',
                'title' => 'Update Progress',
                'description' => "{$period->project->name} - {$period->name}",
                'user' => $period->updatedBy ? $period->updatedBy->name : 'System',
                'timestamp' => $period->updated_at,
                'icon' => 'fa-edit',
                'color' => '#17a2b8',
            ];
        }
        
        // Sort by timestamp dan ambil 5 teratas
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        
        return array_slice($activities, 0, 5);
    }

    private function getImportantNotifications($user)
    {
        $today = now()->startOfDay();
        $notifications = [];
        
        // 1. Periode mendekati deadline (7 hari)
        $upcomingDeadlines = Period::with('project')
            ->where('is_completed', false)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(7))
            ->orderBy('end_date');
        
        if ($user->isUser()) {
            $upcomingDeadlines->where('created_by', $user->id);
        }
        
        foreach ($upcomingDeadlines->get() as $period) {
            // Hitung selisih hari absolute
            // Pastikan end_date juga diambil startOfDay-nya
            $deadlineDate = $period->end_date->copy()->startOfDay();
            $daysLeft = $today->diffInDays($deadlineDate); 
            
            // Opsional: Custom pesan jika hari ini
            $timeText = ($daysLeft === 0) ? "hari ini" : "dalam {$daysLeft} hari";

            $notifications[] = [
                'type' => 'deadline',
                'title' => 'Deadline Mendekat',
                'message' => "{$period->project->name} - {$period->name} akan berakhir {$timeText}",
                'timestamp' => now(),
                'icon' => 'fa-clock',
                'color' => '#ffc107',
                'link' => route('project.description', $period->project_id) . '?period_id=' . $period->id,
            ];
        }
        
        // 2. Progress stagnan (tidak ada update 3 hari)
        $stagnantPeriods = Period::with('project')
            ->where('is_completed', false)
            ->where('updated_at', '<', now()->subDays(3))
            ->orderBy('updated_at');
        
        if ($user->isUser()) {
            $stagnantPeriods->where('created_by', $user->id);
        }
        
        foreach ($stagnantPeriods->limit(3)->get() as $period) {
            $daysSince = now()->diffInDays($period->updated_at);
            $notifications[] = [
                'type' => 'stagnant',
                'title' => 'Progress Stagnan',
                'message' => "{$period->project->name} - {$period->name} belum ada update {$daysSince} hari",
                'timestamp' => $period->updated_at,
                'icon' => 'fa-exclamation-triangle',
                'color' => '#dc3545',
                'link' => route('project.edit', $period->project_id) . '?period_id=' . $period->id,
            ];
        }
        
        // 3. Periode menunggu approval (submitted)
        if ($user->isAdmin()) {
            $pendingApprovals = Period::with('project')
                ->where('status', 'submitted')
                ->orderBy('updated_at', 'desc');
            
            foreach ($pendingApprovals->limit(3)->get() as $period) {
                $notifications[] = [
                    'type' => 'approval',
                    'title' => 'Menunggu Approval',
                    'message' => "{$period->project->name} - {$period->name} perlu di-review",
                    'timestamp' => $period->updated_at,
                    'icon' => 'fa-check-circle',
                    'color' => '#28a745',
                    'link' => route('dashboard'),
                ];
            }
        }
        
        // Sort by priority and timestamp
        usort($notifications, function($a, $b) {
            $priority = ['deadline' => 1, 'approval' => 2, 'stagnant' => 3];
            $aPriority = $priority[$a['type']] ?? 99;
            $bPriority = $priority[$b['type']] ?? 99;
            
            if ($aPriority === $bPriority) {
                return $b['timestamp'] <=> $a['timestamp'];
            }
            return $aPriority <=> $bPriority;
        });
        
        return array_slice($notifications, 0, 5);
    }

    private function getActivityTitle($status)
    {
        return match($status) {
            'submission' => 'Submit untuk Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Update Status'
        };
    }

    private function getActivityIcon($status)
    {
        return match($status) {
            'submission' => 'fa-paper-plane',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            default => 'fa-info-circle'
        };
    }

    private function getActivityColor($status)
    {
        return match($status) {
            'submission' => '#17a2b8',
            'approved' => '#28a745',
            'rejected' => '#dc3545',
            default => '#6c757d'
        };
    }
}
?>