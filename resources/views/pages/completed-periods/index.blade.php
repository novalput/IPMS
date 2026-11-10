@extends('layouts.app')

@section('title', 'Arsip Proyek Selesai')

@section('content')
<div style="padding: 2rem;">

<style>
    /* Styling Accordion - Mengambil Style dari New Project */
    .project-accordion-item {
        background: white;
        border-radius: 12px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
        overflow: hidden;
    }
    
    /* Header Accordion - Sedikit beda warna agar terlihat "Selesai/Arsip" */
    .project-header {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        background: #f8fafc; /* Sedikit lebih gelap dari putih */
        transition: background 0.2s;
    }
    .project-header:hover { background: #f1f5f9; }

    .project-info { flex: 1; }

    .project-title {
        font-size: 18px; font-weight: 600; color: #334155; /* Warna teks agak abu */
        margin-bottom: 8px; display: flex; align-items: center; gap: 10px;
    }

    /* Progress Bar di Header */
    .project-progress-bar-wrapper {
        width: 200px; margin-right: 20px; text-align: right;
    }
    .progress-bar-bg {
        height: 8px; background: #e2e8f0; border-radius: 4px;
        overflow: hidden; margin-bottom: 4px;
    }
    .progress-bar-fill {
        height: 100%; 
        background: linear-gradient(90deg, #16a34a, #22c55e); /* Warna Hijau Full */
    }
    .expand-icon {
        color: #94a3b8; transition: transform 0.3s; margin-left: 15px;
    }

    /* Body Accordion */
    .project-body {
        background: #fff; border-top: 1px solid #e2e8f0;
        display: none; padding: 20px 24px;
    }
    
    .period-table {
        width: 100%; border-collapse: separate; border-spacing: 0 8px;
    }
    .period-row {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
    }
    .period-row td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    
    /* Badge Status */
    .status-badge { 
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; 
        background: #dcfce7; color: #166534; /* Selalu hijau karena completed */
    }
    
    /* Buttons */
    .btn-action { 
        padding: 6px 12px; border-radius: 6px; font-size: 13px; 
        cursor: pointer; margin-left: 4px; transition: 0.2s;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid transparent; text-decoration: none !important;
    }
    .btn-view { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }
    .btn-reactivate { background: #fff7ed; color: #c2410c; border-color: #ffedd5; }
    .btn-reactivate:hover { background: #ffedd5; }

    /* Page Head */
    .page-head {
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;
    }
    
    /* Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1050;
        display: none; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-box {
        background: white; width: 450px; max-width: 90%;
        border-radius: 12px; padding: 24px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        transform: translateY(20px); transition: transform 0.3s ease;
    }
    .modal-overlay.show .modal-box { transform: translateY(0); }
    .modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;}
    .btn-modal { padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none;}
    .btn-modal-cancel { background: #f1f5f9; color: #475569; }
    .btn-modal-warning { background: #f59e0b; color: white; }
</style>

<div class="page-head">
    <div>
        <h2 style="font-size: 24px; font-weight: 700; color: #0f172a;">Arsip Proyek Selesai</h2>
        <p style="color: #64748b;">Daftar proyek yang telah mencapai bobot 1.0 dan progress 100%.</p>
    </div>
    
    @if(auth()->user()->isAdmin())
        <a href="{{ route('project.create-past') }}" class="btn-action" style="background: #f59e0b; color: white; padding: 10px 20px;">
            <i class="fas fa-history" style="margin-right: 8px;"></i> Input Proyek Lampau
        </a>
    @endif
</div>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@forelse($projects as $project)
    <div class="project-accordion-item">
        <div class="project-header" onclick="toggleAccordion(this)">
            <div class="project-info">
                <div class="project-title">
                    <i class="fas fa-check-double" style="color: #16a34a;"></i>
                    {{ $project->name }}
                </div>
                <div style="font-size: 13px; color: #64748b;">
                    {{ $project->periods->count() }} Periode Selesai | Diarsipkan: {{ $project->periods->first()->completed_at ? \Carbon\Carbon::parse($project->periods->first()->completed_at)->format('d M Y') : '-' }}
                </div>
            </div>
            
            <div style="display: flex; align-items: center;">
                <div class="project-progress-bar-wrapper">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: 100%"></div>
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: #166534;">
                        Completed (100%)
                    </span>
                </div>
                <i class="fas fa-chevron-down expand-icon"></i>
            </div>
        </div>

        <div class="project-body">
            <table class="period-table">
                <thead>
                    <tr style="text-align: left; color: #64748b; font-size: 13px;">
                        <th style="padding-left: 16px;">Nama Periode</th>
                        <th>Jadwal</th>
                        <th>Progress & Bobot</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->periods as $period)
                        @php
                            $prog = $period->projectProgress;
                            $val = $prog ? $prog->progress_percentage : 0;
                            $weight = $prog ? $prog->weight : 0;
                        @endphp
                        <tr class="period-row">
                            <td>
                                <strong>{{ $period->name }}</strong>
                            </td>
                            <td style="font-size: 13px; color: #475569;">
                                {{ $period->start_date ? $period->start_date->format('d M Y') : '-' }} s/d 
                                {{ $period->end_date ? $period->end_date->format('d M Y') : '-' }}
                            </td>
                            <td>
                                <div style="font-size: 13px;">
                                    <div>Final: <b>{{ $val }}%</b></div>
                                    <div style="color: #64748b;">Bobot: {{ $weight }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="{{ route('project.description', $project->id) }}?period_id={{ $period->id }}" class="btn-action btn-view" title="Lihat Arsip">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <button type="button" class="btn-action btn-reactivate" 
                                            onclick="showReactivateModal('{{ $period->id }}', '{{ addslashes($project->name . ' - ' . $period->name) }}')" 
                                            title="Kembalikan ke Proyek Aktif">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 2px dashed #e2e8f0;">
        <i class="fas fa-box-open" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
        <h3 style="color: #64748b; font-size: 18px; font-weight: 600;">Belum ada arsip</h3>
        <p style="color: #94a3b8;">Proyek akan muncul di sini jika semua periode disetujui, bobot total 1.0, dan progress 100%.</p>
    </div>
@endforelse

<div id="reactivateModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; color: #c2410c; margin-bottom: 12px;">
            <i class="fas fa-exclamation-circle"></i> Konfirmasi Aktivasi
        </h3>
        <p style="font-size: 14px; color: #64748b; line-height: 1.6;">
            Apakah Anda yakin ingin mengaktifkan kembali periode <b id="reactivatePeriodName"></b>?
            <br><br>
            Periode ini akan <strong>dihapus dari arsip</strong> dan muncul kembali di menu <strong>Proyek Baru</strong> dengan status aktif.
        </p>
        
        <form id="reactivateForm" method="POST" action="">
            @csrf
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-modal-cancel" onclick="closeReactivateModal()">Batal</button>
                <button type="submit" class="btn-modal btn-modal-warning">Ya, Aktifkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Accordion Logic
    function toggleAccordion(element) {
        const body = element.nextElementSibling;
        const icon = element.querySelector('.expand-icon');
        if (body.style.display === "block") {
            body.style.display = "none";
            icon.style.transform = "rotate(0deg)";
        } else {
            body.style.display = "block";
            icon.style.transform = "rotate(180deg)";
        }
    }

    // Modal Logic
    window.showReactivateModal = function(periodId, name) {
        document.getElementById('reactivatePeriodName').textContent = name;
        // Generate URL dinamis
        let url = "{{ route('completed-periods.reactivate', ':id') }}";
        url = url.replace(':id', periodId);
        document.getElementById('reactivateForm').action = url;
        document.getElementById('reactivateModal').classList.add('show');
    };

    window.closeReactivateModal = function() {
        document.getElementById('reactivateModal').classList.remove('show');
    };

    // Close on Outside Click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if(e.target === this) this.classList.remove('show');
        });
    });
</script>

</div>
@endsection