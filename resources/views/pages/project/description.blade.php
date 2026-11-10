@extends('layouts.app')

@section('title', 'Deskripsi Proyek - ' . $project->name)

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
        align-items: center;
    }

    .project-status-badge {
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
        margin-left: 16px;
    }

    .project-status-badge.approved {
        background: #27ae60;
    }
    
    .project-status-badge.completed {
        background: linear-gradient(135deg, #10b981 0%, #06b54a 100%);
        color: #fff;
    }

    .project-status-badge.submission {
        background: #f39c12;
    }

    .project-status-badge.saved {
        background: #6c757d;
    }

    .project-status-badge.submitted_by_konseptor {
        background: #f39c12;
    }

    .project-status-badge.submitted_by_reviewer_contractor_1 {
        background: #3498db;
    }

    .project-status-badge.submitted_by_reviewer_contractor_2 {
        background: #9b59b6;
    }

    .project-status-badge.submitted_by_reviewer_ipms_1 {
        background: #e67e22;
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

    .content-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 32px;
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e7ef;
    }

    .description-content {
        font-size: 16px;
        line-height: 1.6;
        color: #34495e;
    }

    .period-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }

    .info-item {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #00AEEF;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: #2c3e50;
    }

    .no-description {
        text-align: center;
        color: #7f8c8d;
        font-style: italic;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 8px;
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

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }

    .description-section {
        margin-top: 24px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #004ADF;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-badge.approved {
        background: #27ae60;
        color: #fff;
    }

    .status-badge.submission {
        background: #f39c12;
        color: #fff;
    }

    .status-badge.saved {
        background: #6c757d;
        color: #fff;
    }

    .status-badge.submitted_by_konseptor {
        background: #f39c12;
        color: #fff;
    }

    .status-badge.submitted_by_reviewer_contractor_1 {
        background: #3498db;
        color: #fff;
    }

    .status-badge.submitted_by_reviewer_contractor_2 {
        background: #9b59b6;
        color: #fff;
    }

    .status-badge.submitted_by_reviewer_ipms_1 {
        background: #e67e22;
        color: #fff;
    }
    

    /* Progress Chart Styles */
    .progress-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .summary-item {
        background: white;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    
    .summary-item:hover {
        transform: translateY(-2px);
    }
    
    .summary-item.plan {
        border-left: 4px solid #007bff;
    }
    
    .summary-item.actual {
        border-left: 4px solid #28a745;
    }
    
    .summary-item.variance {
        border-left: 4px solid #ffc107;
    }
    
    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    
    .summary-item.plan .summary-icon {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .summary-item.actual .summary-icon {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .summary-item.variance .summary-icon {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .summary-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .summary-label {
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .summary-value {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .summary-value.positive {
        color: #28a745;
    }
    
    .summary-value.negative {
        color: #dc3545;
    }
    
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: relative;
    }
    
    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #004ADF 0%, #00A2E9 100%);
        border-radius: 12px 12px 0 0;
    }
    
    #progressChart {
        width: 100% !important;
        height: 400px !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
        
        .summary-item {
            padding: 16px;
        }
        
        .chart-container {
            padding: 16px;
        }
        
        #progressChart {
            height: 300px !important;
        }
    }
</style>

<div class="project-header">
    <div class="project-info">
        <h1>{{ $project->name }}</h1>
        <div class="project-meta">
            <span>Dibuat: <strong>{{ $project->created_at->format('d M Y') }}</strong></span>
            <span>Total Periode: <strong>{{ $periods->count() }}</strong></span>
            @if($currentPeriod)
            <span class="project-status-badge {{ $currentPeriod->status }}">
                @switch($currentPeriod->status)
                    @case('approved')
                        Approved
                        @break
                    @case('saved')
                        Saved
                        @break
                    @case('submitted')
                        Submitted
                        @break
                    @case('rejected')
                        Rejected
                    @default
                        {{ ucfirst($periods->status) }}
                @endswitch
            </span>
            @if(!empty($currentPeriod->is_completed) && $currentPeriod->is_completed)
                <span class="project-status-badge completed" title="Diselesaikan pada {{ optional($currentPeriod->completed_at)->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}">
                    Selesai
                    @if(!empty($currentPeriod->completed_at))
                        • {{ \Carbon\Carbon::parse($currentPeriod->completed_at)->format('d M Y') }}
                    @endif
                </span>
            @endif
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

<div class="content-section">
    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Nama Proyek:</span>
            <span class="info-value">{{ $project->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Status:</span>
            @if($currentPeriod)
            <span class="project-status-badge {{ $currentPeriod->status }}">
                {{ $currentPeriod->getDisplayStatusForUser(auth()->user()) }}
            </span>
            @endif
        </div>
    </div>
    
    @if($project->description)
    <div class="description-section">
        <h3>Deskripsi Proyek</h3>
        <p>{{ $project->description }}</p>
        <h3>Durasi Periode {{($currentPeriod->name)}}</h3>
        <p>
            @if($currentPeriod)
                {{ $currentPeriod->start_date ? \Carbon\Carbon::parse($currentPeriod->start_date)->format('d M Y') : '-' }} 
                sampai 
                {{ $currentPeriod->end_date ? \Carbon\Carbon::parse($currentPeriod->end_date)->format('d M Y') : '-' }}
            @else
                -
            @endif
    </div>
    @endif
</div>

@if($currentPeriod)
@php
    $creator = optional($currentPeriod->createdBy)->name ?? 'Sistem';
    $creatorPosition = optional($currentPeriod->createdBy)->job_position ?? '-';
    $createdAt = $currentPeriod->created_at
        ? $currentPeriod->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB'
        : '-';
    $hasEdited = $currentPeriod->updated_by && $currentPeriod->updated_at && !$currentPeriod->updated_at->equalTo($currentPeriod->created_at);
    $editor = optional($currentPeriod->updatedBy)->name ?? 'Sistem';
    $editorPosition = optional($currentPeriod->updatedBy)->job_position ?? '-'; // PERBAIKAN: Ambil dari relasi
    $updatedAt = $currentPeriod->updated_at
        ? $currentPeriod->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB'
        : null;
@endphp
<div class="content-section" style="padding: 20px;">
    <div style="background:#f8f9fa;border:1px solid #e0e7ef;border-radius:8px;padding:12px 16px;">
        <div style="margin-bottom:6px;">
            <strong>Periode dibuat oleh:</strong> {{ $creator }} ({{ $creatorPosition }}) pada {{ $createdAt }}
        </div>
        @if($hasEdited)
            <div>
                <strong>Terakhir diubah oleh:</strong> {{ $editor }} ({{ $editorPosition }}) pada {{ $updatedAt }}
            </div>
        @else
            <div>
                <strong>Status perubahan:</strong> Periode ini belum dilakukan perubahan sejak pembuatan.
            </div>
        @endif
    </div>
</div>
@endif

@php
    // Coba ambil relasi projectProgress dari currentPeriod
    // Pastikan di model Period ada relasi: public function projectProgress() { return $this->hasOne(ProjectProgress::class); }
    $pp = $currentPeriod->projectProgress;
@endphp

@if($currentPeriod && $pp)
    @php
        // Hitung manual di view untuk memastikan akurasi
        $finalProgress = floatval($pp->progress_percentage) * floatval($pp->weight);
    @endphp

    <div class="content-section">
        <div class="section-title">
            <i class="fas fa-chart-pie" style="color: #004ADF; margin-right: 8px;"></i>
            Status Progress - {{ $currentPeriod->name }}
        </div>
        
        <div class="progress-details-container" style="display: flex; gap: 24px; flex-wrap: wrap; align-items: stretch;">
            
            {{-- KOLOM KIRI: TOTAL FINAL --}}
            <div style="flex: 1; min-width: 280px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 28px; border-radius: 16px; border: 1px solid #90caf9; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15);">
                
                <h4 style="margin: 0; font-size: 15px; color: #1565c0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                    Total Progress Terbobot
                </h4>
                
                <div style="font-size: 56px; font-weight: 800; color: #0d47a1; margin: 16px 0; line-height: 1;">
                    {{ number_format($finalProgress, 2) }}%
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #1976d2; background: rgba(255,255,255,0.6); padding: 6px 14px; border-radius: 20px;">
                    <i class="fas fa-calculator"></i>
                    <span>Rumus: Input × Bobot</span>
                </div>
            </div>

            {{-- KOLOM KANAN: DETAIL KOMPONEN --}}
            <div style="flex: 2; min-width: 320px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                
                {{-- Detail 1: Input Asli --}}
                <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #6c757d; font-size: 12px; font-weight: 700; text-transform: uppercase;">Input Progress</span>
                        <i class="fas fa-percentage" style="color: #adb5bd;"></i>
                    </div>
                    <div style="font-size: 28px; font-weight: 700; color: #2c3e50;">
                        {{ number_format($pp->progress_percentage, 2) }}%
                    </div>
                    <div style="font-size: 12px; color: #95a5a6; margin-top: 4px;">Aktual Lapangan</div>
                </div>

                {{-- Detail 2: Bobot --}}
                <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #6c757d; font-size: 12px; font-weight: 700; text-transform: uppercase;">Bobot (Weight)</span>
                        <i class="fas fa-weight-hanging" style="color: #adb5bd;"></i>
                    </div>
                    <div style="font-size: 28px; font-weight: 700; color: #2c3e50;">
                        {{ number_format($pp->weight, 2) }}
                    </div>
                    <div style="font-size: 12px; color: #95a5a6; margin-top: 4px;">Faktor Pengali</div>
                </div>
                
                {{-- Detail 3: Notes (Full Width) --}}
                <div style="grid-column: 1 / -1; background: #fffcf5; padding: 20px; border-radius: 12px; border: 1px solid #f5e9c7;">
                    <div style="color: #b78a0b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-sticky-note"></i> Catatan Progress
                    </div>
                    <div style="color: #5c4d28; font-size: 14px; line-height: 1.6; font-style: {{ $pp->notes ? 'normal' : 'italic' }};">
                        {{ $pp->notes ?: 'Tidak ada catatan khusus yang ditambahkan untuk update progress ini.' }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@else
    {{-- TAMPILAN JIKA BELUM ADA DATA PROGRESS --}}
    <div class="content-section">
        <div class="section-title">
            <i class="fas fa-chart-pie" style="color: #004ADF; margin-right: 8px;"></i>
            Status Progress
        </div>
        <div class="no-description" style="text-align: center; padding: 40px; color: #95a5a6; background: #f8f9fa; border-radius: 12px; border: 2px dashed #e9ecef;">
            <i class="fas fa-chart-bar" style="font-size: 48px; color: #bdc3c7; margin-bottom: 16px; display: block;"></i>
            <p style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">Belum ada data progress</p>
            <small>Silahkan lakukan update progress atau edit proyek untuk memunculkan data.</small>
        </div>
    </div>
@endif

<script>
function changePeriod(periodId) {
    // Preserve search params when changing period
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('period_id', periodId);
    window.location.href = "{{ route('project.description', $project->id) }}?" + urlParams.toString();
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
        // Normal navigation - go to list page
        backButton.href = "{{ route('new_project.index') }}";
        backButton.innerHTML = '<i class="fas fa-arrow-left"></i> Kembali';
    }
});
</script>

</div>
@endsection