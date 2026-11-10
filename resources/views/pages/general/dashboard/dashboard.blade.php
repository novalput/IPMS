@extends('layouts.app')

@section('title', 'Dashboard - Integrated Project Management System')

@section('content')
<div class="dashboard-wrapper">
<style>
    .dashboard-wrapper {
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    /* 1. STATISTICS CARDS */
    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }
    
    .stat-icon.total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .stat-icon.active {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .stat-icon.completed {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    
    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }
    
    .stat-content p {
        margin: 0.25rem 0 0;
        color: #7f8c8d;
        font-size: 0.95rem;
    }
    
    /* 2. PROGRESS PROYEK */
    .progress-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .progress-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
        table-layout: fixed;
    }
    
    .progress-table thead th {
        text-align: left;
        padding: 0.75rem 1rem;
        color: #7f8c8d;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .progress-table tbody tr {
        background: #f8f9fa;
        transition: all 0.2s;
    }
    
    .progress-table tbody tr:hover {
        background: #e9ecef;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .progress-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .progress-table tbody td:first-child {
        border-radius: 8px 0 0 8px;
        text-align: center;
        font-weight: 600;
        color: #7f8c8d;
    }
    
    .progress-table tbody td:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .project-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .project-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
    }
    
    .period-name {
        font-size: 0.85rem;
        color: #7f8c8d;
    }
    
    .chart-cell {
        text-align: center;
    }
    
    .progress-display {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .progress-bar-container {
        flex: 1;
        width: 140px;
        height: 8px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #00AEEF 0%, #004ADF 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .progress-percentage {
        font-weight: 600;
        color: #2c3e50;
        min-width: 50px;
        width: 55px;
        text-align: right;
    }
    
    .deadline-cell {
        text-align: center;
    }
    
    .deadline-date {
        font-weight: 500;
        color: #2c3e50;
    }
    
    .deadline-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }
    
    .deadline-badge.urgent {
        background: #fff3cd;
        color: #856404;
    }
    
    .deadline-badge.normal {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    /* 3. AKTIVITAS & NOTIFIKASI */
    .activity-notification-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .activity-box, .notification-box {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .activity-item, .notification-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: background 0.2s;
    }
    
    .activity-item:hover, .notification-item:hover {
        background: #f8f9fa;
    }
    
    .activity-icon, .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 16px;
        color: white;
    }
    
    .activity-content, .notification-content {
        flex: 1;
        min-width: 0;
    }
    
    .activity-title, .notification-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.25rem;
        font-size: 0.95rem;
    }
    
    .activity-desc, .notification-message {
        color: #7f8c8d;
        font-size: 0.85rem;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .activity-meta {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-top: 0.25rem;
        font-size: 0.8rem;
        color: #95a5a6;
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #95a5a6;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* 4. QUICK ACTIONS */
    .quick-actions-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .action-btn {
        background: white;
        border-radius: 12px;
        padding: 2rem 1.5rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    
    .action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        text-decoration: none;
        color: inherit;
    }
    
    .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .action-btn.secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .action-btn.tertiary {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    
    .action-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .action-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }
    
    .action-desc {
        font-size: 0.85rem;
        margin: 0.5rem 0 0;
        opacity: 0.9;
    }
    
    @media (max-width: 768px) {
        .dashboard-wrapper {
            padding: 1rem;
        }
        
        .activity-notification-section {
            grid-template-columns: 1fr;
        }
        
        .stats-section {
            grid-template-columns: 1fr;
        }
    }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 2rem;">{{ session('success') }}</div>
@endif

<!-- 1. STATISTIK CARDS -->
<div class="stats-section">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-project-diagram"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $statistics['total'] }}</h3>
            <p>Total Proyek</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon active">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $statistics['active'] }}</h3>
            <p>Proyek Aktif</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon completed">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $statistics['completed'] }}</h3>
            <p>Proyek Selesai</p>
        </div>
    </div>
</div>

<!-- 2. PROGRESS PROYEK -->
<div class="progress-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-chart-line"></i> Progress Proyek</h2>
    </div>
    
    @if(count($projectProgress) > 0)
        <table class="progress-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Judul Proyek</th>
                    <th style="width: 200px;">Chart Progress</th>
                    <th style="width: 150px;">Progress</th>
                    <th style="width: 150px; text-align: center;">Deadline</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projectProgress as $index => $progress)
                <tr onclick="window.location='{{ route('project.description', $progress['project_id']) }}?period_id={{ $progress['period_id'] }}'" style="cursor: pointer;">
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="project-info">
                            <span class="project-name">{{ $progress['project_name'] }}</span>
                            <span class="period-name">{{ $progress['period_name'] }}</span>
                        </div>
                    </td>
                    <td class="chart-cell">
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $progress['total_progress'] }}%"></div>
                        </div>
                    
                    </td>
                    <td>
                        <div class="progress-display">
                            <span class="progress-percentage">{{ number_format($progress['total_progress'], 1) }}%</span>
                        </div>
                    </td>
                    <td class="deadline-cell">
                        <div class="deadline-date">{{ $progress['deadline'] }}</div>
                        @if($progress['deadline_raw'])
                            @php
                                // [UBAH DISINI]
                                // 1. Reset waktu 'sekarang' ke 00:00:00
                                // 2. Reset waktu 'deadline' ke 00:00:00
                                // 3. Hitung selisih mutlak dalam integer
                                $now = now()->startOfDay();
                                $deadline = \Carbon\Carbon::parse($progress['deadline_raw'])->startOfDay();
                                $daysLeft = $now->diffInDays($deadline, false); // false agar bisa negatif jika lewat (meski sudah difilter di controller)
                            @endphp

                            @if($daysLeft == 0)
                                <span class="deadline-badge urgent">Hari Ini</span>
                            @elseif($daysLeft > 0 && $daysLeft <= 7)
                                <span class="deadline-badge urgent">{{ $daysLeft }} hari lagi</span>
                            @elseif($daysLeft > 7)
                                <span class="deadline-badge normal">{{ $daysLeft }} hari lagi</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Belum ada proyek aktif</p>
        </div>
    @endif
</div>

<!-- 3. AKTIVITAS & NOTIFIKASI -->
<div class="activity-notification-section">
    <!-- AKTIVITAS TERBARU -->
    <div class="activity-box">
        <div class="section-header">
            <h2 class="section-title" style="font-size: 1.25rem;"><i class="fas fa-history"></i> Aktivitas Terbaru</h2>
        </div>
        
        @if(count($recentActivities) > 0)
            @foreach($recentActivities as $activity)
            <div class="activity-item">
                <div class="activity-icon" style="background: {{ $activity['color'] }};">
                    <i class="fas {{ $activity['icon'] }}"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-title">{{ $activity['title'] }}</p>
                    <p class="activity-desc">{{ $activity['description'] }}</p>
                    <div class="activity-meta">
                        <span><i class="fas fa-user"></i> {{ $activity['user'] }}</span>
                        <span>•</span>
                        <span><i class="fas fa-clock"></i> {{ $activity['timestamp']->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>Belum ada aktivitas</p>
            </div>
        @endif
    </div>
    
    <!-- NOTIFIKASI PENTING -->
    <div class="notification-box">
        <div class="section-header">
            <h2 class="section-title" style="font-size: 1.25rem;"><i class="fas fa-bell"></i> Notifikasi Penting</h2>
        </div>
        
        @if(count($importantNotifications) > 0)
            @foreach($importantNotifications as $notif)
            <a href="{{ $notif['link'] }}" style="text-decoration: none; color: inherit;">
                <div class="notification-item">
                    <div class="notification-icon" style="background: {{ $notif['color'] }};">
                        <i class="fas {{ $notif['icon'] }}"></i>
                    </div>
                    <div class="notification-content">
                        <p class="notification-title">{{ $notif['title'] }}</p>
                        <p class="notification-message">{{ $notif['message'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-bell"></i>
                <p>Tidak ada notifikasi</p>
            </div>
        @endif
    </div>
</div>

<!-- 4. QUICK ACTIONS -->
<div class="quick-actions-section">
    <a href="{{ route('project.create') }}" class="action-btn primary">
        <div class="action-icon"><i class="fas fa-plus-circle"></i></div>
        <p class="action-title">Tambah Proyek Baru</p>
        <p class="action-desc">Buat proyek dan periode baru</p>
    </a>
    
    <a href="{{ route('tracking') }}" class="action-btn secondary">
        <div class="action-icon"><i class="fas fa-route"></i></div>
        <p class="action-title">Lihat Tracking</p>
        <p class="action-desc">Monitor progress approval</p>
    </a>
    
    <a href="{{ route('completed-periods.index') }}" class="action-btn tertiary">
        <div class="action-icon"><i class="fas fa-archive"></i></div>
        <p class="action-title">Lihat Proyek Lama</p>
        <p class="action-desc">Arsip proyek yang sudah selesai</p>
    </a>
</div>

</div>
@endsection