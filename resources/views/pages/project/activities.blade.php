@extends('layouts.app')

@section('title', 'Aktivitas Proyek - ' . $project->name)

@section('content')
<div style="padding: 1rem;">
<style>
    .project-header {
        background: linear-gradient(135deg, #004ADF 0%, #00A2E9 100%);
        color: #fff;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .project-info h1 {
        font-size: 28px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }

    .project-meta {
        display: flex;
        gap: 24px;
        font-size: 14px;
        opacity: 0.9;
    }

    .period-selector {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 12px 16px;
        min-width: 200px;
    }

    .period-dropdown {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 16px;
        font-weight: 500;
        width: 100%;
        outline: none;
    }

    .period-dropdown option {
        background: #004ADF;
        color: #fff;
    }

    .scope-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .scope-header {
        background: #00AEEF;
        color: #fff;
        padding: 16px 24px;
        font-size: 18px;
        font-weight: 600;
    }

    .scope-content {
        padding: 24px;
    }

    .activities-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .activities-table th,
    .activities-table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #e0e7ef;
        font-size: 14px;
    }

    .activities-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
    }

    .activities-table td:nth-child(2),
    .activities-table td:nth-child(3),
    .activities-table td:nth-child(5) {
        text-align: left;
    }

    .activities-table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-ongoing {
        background: #fff3cd;
        color: #856404;
    }

    .status-done {
        background: #d4edda;
        color: #155724;
    }

    .status-delayed {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    /* Back Button Styles */
    .back-button {
        background: rgba(255,255,255,0.95) !important;
        color: #004ADF !important;
        border: 2px solid rgba(255,255,255,1) !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        margin-right: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .back-button:hover {
        background: rgba(255,255,255,1) !important;
        color: #002e8f !important;
        border-color: rgba(255,255,255,1) !important;
        text-decoration: none !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .back-button i {
        font-size: 16px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }
</style>

<div class="project-header">
    <div class="project-info">
        <h1>{{ $project->name }}</h1>
        <div class="project-meta">
            <span>Aktivitas Proyek</span>
            @if($currentPeriod)
                <span>Periode: <strong>{{ $currentPeriod->name }}</strong></span>
            @endif
        </div>
    </div>

    <div class="header-right">
    <a href="#" class="back-button" id="backButton">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </a>
        
        @if($periods->count() > 1)
            <div class="period-selector">
                <select class="period-dropdown" onchange="changePeriod(this.value)">
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ $currentPeriod && $currentPeriod->id == $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                            @if($period->start_date && $period->end_date)
                                ({{ \Carbon\Carbon::parse($period->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d/m/Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>

@if($currentPeriod)
    @php
        $activitiesByScope = $currentPeriod->activities->groupBy('scope');
        $scopes = $activitiesByScope->keys()->sort();
    @endphp

    @if($scopes->count() > 0)
        @foreach($scopes as $scope)
            <div class="scope-section">
                <div class="scope-header">Scope {{ strtoupper($scope) }}</div>
                <div class="scope-content">
                    <table class="activities-table">
                        <thead>
                            <tr>
                                <th style="width:40px">No</th>
                                <th>Lokasi</th>
                                <th>Capaian Minggu Ini</th>
                                <th style="width:100px">Progress (%)</th>
                                <th>Rencana Minggu Depan</th>
                                <th style="width:120px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activitiesByScope[$scope] as $index => $activity)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $activity->location }}</td>
                                    <td>{{ $activity->achievement_this_week ?: '-' }}</td>
                                    <td>{{ number_format($activity->progress, 1) }}%</td>
                                    <td>{{ $activity->plan_next_week ?: '-' }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $activity->status }}">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="scope-section">
            <div class="scope-content">
                <div class="empty-state">
                    <p>Belum ada aktivitas untuk periode ini.</p>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="scope-section">
        <div class="scope-content">
            <div class="empty-state">
                <p>Proyek ini belum memiliki periode. Silakan buat periode terlebih dahulu.</p>
            </div>
        </div>
    </div>
@endif

<script>
function changePeriod(periodId) {
    // Preserve search params when changing period
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('period_id', periodId);
    
    // Update route name sesuai halaman
    @if(Request::route()->getName() === 'project.progress')
        window.location.href = "{{ route('project.progress', $project->id) }}?" + urlParams.toString();
    @elseif(Request::route()->getName() === 'project.activities')
        window.location.href = "{{ route('project.activities', $project->id) }}?" + urlParams.toString();
    @elseif(Request::route()->getName() === 'project.issues')
        window.location.href = "{{ route('project.issues', $project->id) }}?" + urlParams.toString();
    @elseif(Request::route()->getName() === 'project.documentation')
        window.location.href = "{{ route('project.documentation', $project->id) }}?" + urlParams.toString();
    @else
        window.location.href = "{{ route('project.description', $project->id) }}?" + urlParams.toString();
    @endif
}

// Smart back button functionality
document.addEventListener('DOMContentLoaded', function() {
    const backButton = document.getElementById('backButton');
    
    // Check if user came from search results
    const urlParams = new URLSearchParams(window.location.search);
    const referrer = document.referrer;
    
    // Check if there are search parameters in the URL or if referrer contains search
    if (urlParams.has('from_search') || 
        (referrer && referrer.includes('/search')) || 
        (urlParams.has('search') || urlParams.has('filter') || urlParams.has('start_date') || urlParams.has('end_date'))) {
        
        // User came from search - go back to search with preserved params
        let searchUrl = "{{ route('search') }}";
        let searchParams = new URLSearchParams();
        
        // Preserve search parameters
        if (urlParams.has('search')) searchParams.set('search', urlParams.get('search'));
        if (urlParams.has('filter')) searchParams.set('filter', urlParams.get('filter'));
        if (urlParams.has('start_date')) searchParams.set('start_date', urlParams.get('start_date'));
        if (urlParams.has('end_date')) searchParams.set('end_date', urlParams.get('end_date'));
        
        if (searchParams.toString()) {
            searchUrl += '?' + searchParams.toString();
        }
        
        backButton.href = searchUrl;
        backButton.innerHTML = '<i class="fas fa-arrow-left"></i> Kembali ke Hasil Pencarian';
        
    } else {
        backButton.href = "{{ route('new_project.index') }}";
        backButton.innerHTML = '<i class="fas fa-arrow-left"></i> Kembali';
    }
});
</script>
</div>
@endsection