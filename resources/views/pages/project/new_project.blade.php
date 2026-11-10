@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
<div style="padding: 2rem;">

<style>
    /* Styling Accordion */
    .project-accordion-item {
        background: white;
        border-radius: 12px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
        overflow: hidden;
    }
    
    /* Header Accordion */
    .project-header {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        background: #fff;
        transition: background 0.2s;
    }
    .project-header:hover { background: #f8fafc; }

    .project-info { flex: 1; }

    .project-title {
        font-size: 18px; font-weight: 600; color: #1e293b;
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
        height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb);
    }
    .expand-icon {
        color: #94a3b8; transition: transform 0.3s; margin-left: 15px;
    }

    /* Body Accordion */
    .project-body {
        background: #f8fafc; border-top: 1px solid #e2e8f0;
        display: none; padding: 20px 24px;
    }
    
    .period-table {
        width: 100%; border-collapse: separate; border-spacing: 0 8px;
    }
    .period-row {
        background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-radius: 8px;
    }
    .period-row td { padding: 16px; vertical-align: middle; }
    .period-row td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .period-row td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

    /* Helper Classes */
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-saved { background: #f1f5f9; color: #475569; }
    .status-submitted { background: #dbeafe; color: #1e40af; }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    
    /* Buttons in Action Column */
    .btn-action { 
        padding: 6px 12px; border-radius: 6px; font-size: 13px; 
        cursor: pointer; margin-left: 4px; transition: 0.2s;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid transparent;
    }
    .btn-view { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }
    .btn-edit { background: #fefce8; color: #a16207; border-color: #fef9c3; }
    .btn-delete { background: #fee2e2; color: #b91c1c; border-color: #fecaca; } /* Tombol Hapus Periode */
    .btn-reset { background: #fff1f2; color: #be123c; border-color: #fda4af; } /* Tombol Reject */
    .btn-approve { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    
    .btn-action:hover { filter: brightness(0.95); transform: translateY(-1px); }

    /* Page Header */
    .page-head {
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;
    }
    .btn-add-project {
        background: #00AEEF; color: white; padding: 10px 20px;
        border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 6px rgba(0, 174, 239, 0.2); transition: 0.2s;
    }
    .btn-add-project:hover { background: #0095cd; transform: translateY(-2px); }

    /* Modals */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1050;
        display: none; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-box {
        background: white; width: 400px; max-width: 90%;
        border-radius: 12px; padding: 24px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        transform: translateY(20px); transition: transform 0.3s ease;
    }
    .modal-overlay.show .modal-box { transform: translateY(0); }
    .modal-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 12px; }
    .modal-text { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 12px; }
    .btn-modal { padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; }
    .btn-modal-cancel { background: #f1f5f9; color: #475569; }
    .btn-modal-approve { background: #16a34a; color: white; }
    .btn-modal-reject { background: #dc2626; color: white; }
</style>

<div class="page-head">
    <div>
        <h2 style="font-size: 24px; font-weight: 700; color: #0f172a;">Daftar Proyek Aktif</h2>
        <p style="color: #64748b;">Klik pada baris proyek untuk melihat daftar periode.</p>
    </div>
    
    <a href="{{ route('project.create') }}" class="btn-add-project">
        <i class="fas fa-plus"></i> Tambah Proyek
    </a>
</div>

@forelse($projects as $project)
    <div class="project-accordion-item">
        <div class="project-header" onclick="toggleAccordion(this)">
            <div class="project-info">
                <div class="project-title">
                    <i class="fas fa-folder text-blue-500"></i>
                    {{ $project->name }}
                </div>
                <div style="font-size: 13px; color: #64748b;">
                    {{ $project->periods->count() }} Periode Aktif | Terakhir update: {{ $project->updated_at->format('d M Y') }}
                </div>
            </div>
            
            <div style="display: flex; align-items: center;">
                <div class="project-progress-bar-wrapper">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $project->total_accumulated_progress }}%"></div>
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: #475569;">
                        Total: {{ number_format($project->total_accumulated_progress, 1) }}%
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
                            
                            $statusClass = 'status-' . $period->status;
                            $statusLabel = ucfirst($period->status);
                            
                            // Cek status rejected
                            if($period->status == 'saved') {
                                $lastTrack = \App\Models\Tracking::where('period_id', $period->id)->latest()->first();
                                if($lastTrack && $lastTrack->status == 'rejected') {
                                    $statusClass = 'status-rejected';
                                    $statusLabel = 'Perlu Revisi';
                                } else {
                                    $statusLabel = 'Draft';
                                }
                            } elseif ($period->status == 'submitted') {
                                $statusLabel = 'Menunggu Review';
                            } elseif ($period->status == 'approved') {
                                $statusLabel = 'Disetujui';
                            }
                        @endphp
                        <tr class="period-row">
                            <td>
                                <strong>{{ $period->name }}</strong>
                            </td>
                            <td style="font-size: 13px; color: #475569;">
                                {{ $period->start_date ? $period->start_date->format('d M') : '-' }} s/d 
                                {{ $period->end_date ? $period->end_date->format('d M Y') : '-' }}
                            </td>
                            <td>
                                <div style="font-size: 13px;">
                                    <div>Input: <b>{{ $val }}%</b></div>
                                    <div style="color: #64748b;">Bobot: {{ $weight }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="{{ route('project.description', $project->id) }}?period_id={{ $period->id }}" class="btn-action btn-view" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($period->status == 'saved' && (auth()->user()->isUser()) || auth()->user()->isAdmin())
                                    <a href="{{ route('project.edit', $project->id) }}?period_id={{ $period->id }}" class="btn-action btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <button type="button" class="btn-action btn-delete" onclick="showDeletePeriodModal('{{ $period->id }}', '{{ $period->name }}')" title="Hapus Periode">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                @endif

                                @if(auth()->user()->isAdmin() && $period->status == 'submitted')
                                    <button type="button" class="btn-action btn-approve" onclick="showApproveModal('{{ $project->id }}', '{{ $period->id }}', '{{ $period->name }}')" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif

                                @if(auth()->user()->isAdmin() && ($period->status == 'submitted' || $period->status == 'approved'))
                                    <button type="button" class="btn-action btn-reset" onclick="showRejectModal('{{ $project->id }}', '{{ $period->id }}', '{{ $period->name }}')" title="{{ $period->status == 'approved' ? 'Reset Status (Batal Approval)' : 'Reject (Kembalikan ke Revisi)' }}">
                                        <i class="fas fa-undo"></i> </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div style="text-align: center; padding: 40px; color: #94a3b8;">
        <i class="fas fa-folder-open" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Belum ada proyek aktif.</p>
        <a href="{{ route('project.create') }}" class="btn-add-project" style="display:inline-flex; margin-top:16px;">
            <i class="fas fa-plus"></i> Buat Proyek Baru
        </a>
    </div>
@endforelse


<div id="approveModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title">Konfirmasi Approval</h3>
        <p class="modal-text">
            Apakah Anda yakin ingin menyetujui periode <b id="approvePeriodName"></b>? 
            <br>Status akan berubah menjadi <b>Approved</b>.
        </p>
        <form id="approveForm" method="POST" action="">
            @csrf
            <input type="hidden" name="period_id" id="approvePeriodIdInput">
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-modal-cancel" onclick="closeApproveModal()">Batal</button>
                <button type="submit" class="btn-modal btn-modal-approve">Ya, Approve</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title" style="color: #dc2626;">Konfirmasi Reject</h3>
        <p class="modal-text">
            Apakah Anda yakin ingin menolak periode <b id="rejectPeriodName"></b>?
            <br>Status akan dikembalikan ke <b>Draft</b> agar user bisa merevisi.
        </p>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <input type="hidden" name="period_id" id="rejectPeriodIdInput">
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-modal-cancel" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn-modal btn-modal-reject">Ya, Reject</button>
            </div>
        </form>
    </div>
</div>

<div id="deletePeriodModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title" style="color: #dc2626;">Hapus Periode?</h3>
        <p class="modal-text">
            Anda akan menghapus periode <b id="deletePeriodName"></b>.
            <br>Data detail (EPC, Activity, dll) akan hilang permanen.
        </p>
        <form id="deletePeriodForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-modal-cancel" onclick="closeDeletePeriodModal()">Batal</button>
                <button type="submit" class="btn-modal btn-modal-reject">Hapus</button>
            </div>
        </form>
    </div>
</div>


<script>
    // Accordion
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

    // --- APPROVE ---
    window.showApproveModal = function(projectId, periodId, name) {
        document.getElementById('approveForm').action = "{{ url('/project') }}/" + projectId + "/approve";
        document.getElementById('approvePeriodIdInput').value = periodId;
        document.getElementById('approvePeriodName').textContent = name;
        document.getElementById('approveModal').classList.add('show');
    };
    window.closeApproveModal = function() {
        document.getElementById('approveModal').classList.remove('show');
    };

    // --- REJECT ---
    window.showRejectModal = function(projectId, periodId, name) {
        document.getElementById('rejectForm').action = "{{ url('/project') }}/" + projectId + "/reject";
        document.getElementById('rejectPeriodIdInput').value = periodId;
        document.getElementById('rejectPeriodName').textContent = name;
        document.getElementById('rejectModal').classList.add('show');
    };
    window.closeRejectModal = function() {
        document.getElementById('rejectModal').classList.remove('show');
    };

    // --- DELETE PERIODE ---
    window.showDeletePeriodModal = function(periodId, name) {
        // Asumsi route delete periode adalah /periods/{id}
        // Jika route Anda berbeda, sesuaikan di sini (misal: /project/delete-period/{id})
        document.getElementById('deletePeriodForm').action = "{{ url('/periods') }}/" + periodId;
        document.getElementById('deletePeriodName').textContent = name;
        document.getElementById('deletePeriodModal').classList.add('show');
    };
    window.closeDeletePeriodModal = function() {
        document.getElementById('deletePeriodModal').classList.remove('show');
    };

    // Close Overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if(e.target === this) this.classList.remove('show');
        });
    });
</script>

</div>
@endsection