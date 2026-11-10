@extends('layouts.app')

@section('title', 'Edit Proyek')

@section('content')

<script>
    window.projectData = {
        globalDocIndex: {{ $globalDocIndex ?? 1000 }},
        lastScopeIdx: {{ $lastScopeIdx ?? 0 }},
        originalProjectName: @json(old('project_name', $project->name)),
        isReadOnly: {{ ($period && $period->status==='submitted' && auth()->user()->role==='konseptor') ? 'true' : 'false' }}
    };
</script>

@vite([
    'resources/css/app/project-edit.css',
    'resources/js/app/project-edit.js'
])

<div class="form-create-project">
    @php
        $locked = ($period && $period->status === 'submitted' && auth()->user()->role === 'konseptor');
    @endphp
    
    <!-- Header dengan garis penuh -->
    <div class="page-header-full">
        <h2 class="page-title-full">Edit Proyek</h2>
    </div>

    @php
        $user = auth()->user();
        $locked = ($period && !$period->canBeEditedBy($user));
        $buttonText = 'Submit';
        
        // Tentukan text button berdasarkan role
        if ($user->isUser()) {
            $buttonText = 'Submit';
        } elseif ($user->canApprove($period->status ?? 'saved')) {
            $buttonText = 'Approve';
        }
        $showSubmitButton = !$user->isAdmin() && ($user->isUser() || $period->canBeApprovedBy($user));

    @endphp
    <!-- Action buttons di bawah header -->
    <div class="action-buttons-row">
        @if($locked)
            <button type="button" class="btn-locked">
                <i class="fa fa-lock"></i> Locked - {{ $period->getDisplayStatusForUser($user) }}
            </button>
        @else
            <button type="button" class="btn-draft" id="btnSaveDraft">
                <i class="fa fa-save"></i> Save (Draft)
            </button>
            @if($showSubmitButton)
                <button type="button" class="btn-submit" id="btnSubmit">
                    <i class="fa fa-paper-plane"></i> 
                    {{ $user->isUser() ? 'Submit' : 'Approve' }}
                </button>
            @endif
        @endif
    </div>
    <!-- Update form validation message -->
    @if($locked)
        <div class="alert alert-info" style="margin-bottom:16px;">
            Periode dalam status "{{ $period->getDisplayStatusForUser($user) }}" dan tidak dapat diedit oleh role Anda.
            <br><small>Status internal: {{ $period->status }}</small>
        </div>
    @endif

    <form method="POST" action="{{ route('project.update', [$project->id, optional($period)->id]) }}" id="projectForm" autocomplete="off">
        @csrf
        @method('PUT')
        <input type="hidden" name="period_action" id="periodAction" value="save">
        
        @if($period)
        <input type="hidden" name="period_id" value="{{ $period->id }}">
        @endif

        @if($period && $period->status === 'submitted' && auth()->user()->role === 'konseptor')
            <div class="alert alert-info" style="margin-bottom:16px;">
                Periode sudah submitted. Anda tidak dapat mengedit (read only).
            </div>
        @endif

        <!-- Nama Proyek -->
        <div class="project-header">
            <label class="section-label">Nama Proyek</label>
            <input id="projectInput" name="project_name" type="text" class="form-control" value="{{ old('project_name', $project->name) }}" placeholder="Masukkan nama proyek" style="width:420px;"/>
            <div class="dropdown-wrap" style="margin-left:4px;">
                <select class="dropdown-select" id="projectSelect" name="project_id">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                    @endforeach
                </select>
                <span class="dropdown-caret"><i class="fa fa-caret-down"></i></span>
            </div>
            <button type="button" class="btn-action-primary" id="copyProjectTemplateBtn" style="margin-left:8px;">Copy Template</button>
        </div>

        <div class="period-header">
            <label class="section-label">Periode</label>
            <input id="periodInput" name="period_name" type="text" class="form-control" value="{{ old('period_name', optional($period)->name) }}" placeholder="Nama periode baru (ketik atau pilih)" style="width:420px;" />
            <div class="dropdown-wrap" style="margin-left:4px;">
                <select class="dropdown-select" id="periodSelect">
                    <option value="">-- Pilih Periode --</option>

                        @foreach($project->periods as $prd)
                            <option value="{{ $prd->id }}" {{ optional($period)->id === $prd->id ? 'selected' : '' }}> {{ $prd->name }}</option>
                        @endforeach
                    
                </select>
                <span class="dropdown-caret"><i class="fa fa-caret-down"></i></span>
            </div>
            <button type="button" class="btn-action-primary" id="copyPeriodTemplateBtn" style="margin-left:8px;">Copy Template</button>
        </div>

        <div class="period-range">
            <label style="min-width:120px"></label>
            <input type="date" id="startDate" name="start_date" class="form-control" value="{{ old('start_date', $period && $period->start_date ? $period->start_date->format('Y-m-d') : '') }}">
    <input type="date" id="endDate" name="end_date" class="form-control" value="{{ old('end_date', $period && $period->end_date ? $period->end_date->format('Y-m-d') : '') }}">
        </div>

        <div id="templatePreview" class="mt-4"></div>

        <input type="hidden" name="current_step" id="currentStep" value="1">
        <input type="hidden" name="created_period_name" id="createdPeriodName" value="">
        <input type="hidden" name="created_period_from" id="createdPeriodFrom" value="">
        <input type="hidden" name="created_period_to" id="createdPeriodTo" value="">

        <div class="section-header">Deskripsi Proyek</div>
        <div class="form-section">
            <textarea name="description" class="form-control text-description" placeholder="Masukkan deskripsi proyek">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="section-header">Progress Proyek</div>
            <div class="section-frame" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem;">
                
                <!-- Info Box -->
                <div class="info-box" style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <i class="fas fa-info-circle" style="color: #2196f3; font-size: 1.5rem; margin-top: 0.25rem;"></i>
                        <div>
                            <strong style="color: #1976d2;">Cara Menghitung Progress:</strong>
                            <p style="margin: 0.5rem 0 0; color: #424242; line-height: 1.6;">
                                Progress Akhir = Progress (%) × Bobot<br>
                                <em style="font-size: 0.9rem;">Contoh: Progress 100% dengan bobot 0.7 = 70% progress akhir</em>
                            </p>
                        </div>
                    </div>
                </div>

                @php
                    $progressData = $period && $period->projectProgress ? $period->projectProgress : null;
                @endphp

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <!-- Progress Percentage -->
                    <div class="form-group">
                        <label for="progress_percentage" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                            Progress (%)
                            <span style="color: #e74c3c;">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="progress_percentage" 
                            name="progress_percentage" 
                            class="form-control" 
                            min="0" 
                            max="100" 
                            step="0.01"
                            value="{{ old('progress_percentage', $progressData ? $progressData->progress_percentage : 0) }}"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e6ed; border-radius: 8px; font-size: 1rem;"
                            required
                            oninput="calculateProgress()"
                            {{ $locked ? 'disabled' : '' }}
                        >
                        <small style="color: #7f8c8d; font-size: 0.85rem;">Progress aktual periode ini (0-100)</small>
                    </div>

                    <!-- Weight -->
                    <div class="form-group">
                        <label for="weight" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                            Bobot Periode
                            <span style="color: #e74c3c;">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="weight" 
                            name="weight" 
                            class="form-control" 
                            min="0" 
                            max="1" 
                            step="0.01"
                            value="{{ old('weight', $progressData ? $progressData->weight : 0) }}"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e6ed; border-radius: 8px; font-size: 1rem;"
                            required
                            oninput="calculateProgress()"
                            {{ $locked ? 'disabled' : '' }}
                        >
                        <small style="color: #7f8c8d; font-size: 0.85rem;">Bobot dari total project (0-1)</small>
                    </div>

                    <!-- Calculated Progress (Read-only) -->
                    <div class="form-group">
                        <label for="calculated_progress" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                            Progress Akhir
                        </label>
                        <input 
                            type="text" 
                            id="calculated_progress" 
                            class="form-control" 
                            readonly
                            value="{{ $progressData ? $progressData->calculated_progress . '%' : '0%' }}"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; background: #f8f9fa; font-weight: 600; color: #00AEEF;"
                        >
                        <small style="color: #7f8c8d; font-size: 0.85rem;">Progress × Bobot (auto-calculated)</small>
                    </div>
                </div>

                <!-- Visual Preview -->
                <div class="progress-preview" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: 600; color: #2c3e50;">Preview Progress:</span>
                            <span id="preview_percentage" style="font-size: 1.25rem; font-weight: 700; color: #00AEEF;">{{ $progressData ? $progressData->calculated_progress : 0 }}%</span>
                        </div>
                        <div style="height: 12px; background: #e9ecef; border-radius: 10px; overflow: hidden;">
                            <div id="preview_bar" style="height: 100%; background: linear-gradient(90deg, #00AEEF 0%, #004ADF 100%); width: {{ $progressData ? $progressData->calculated_progress : 0 }}%; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                    <div style="font-size: 0.9rem; color: #7f8c8d;">
                        <strong>Kalkulasi:</strong> 
                        <span id="calc_formula">
                            {{ $progressData ? $progressData->progress_percentage : 0 }}% × 
                            {{ $progressData ? $progressData->weight : 0 }} = 
                            {{ $progressData ? $progressData->calculated_progress : 0 }}%
                        </span>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label for="progress_notes" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                        Catatan Progress (Opsional)
                    </label>
                    <textarea 
                        id="progress_notes" 
                        name="progress_notes" 
                        rows="3" 
                        class="form-control"
                        placeholder="Tambahkan catatan mengenai progress periode ini..."
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e0e6ed; border-radius: 8px; font-size: 0.95rem; resize: vertical;"
                        {{ $locked ? 'disabled' : '' }}
                    >{{ old('progress_notes', $progressData ? $progressData->notes : '') }}</textarea>
                    <small style="color: #7f8c8d; font-size: 0.85rem;">Catatan opsional untuk dokumentasi progress</small>
                </div>

                @if($period && $period->project)
                    @php
                        $otherPeriods = $period->project->periods()
                            ->where('id', '!=', $period->id)
                            ->whereHas('projectProgress')
                            ->with('projectProgress')
                            ->get();
                        
                        $totalWeight = $otherPeriods->sum(function($p) {
                            return $p->projectProgress ? $p->projectProgress->weight : 0;
                        });
                    @endphp
                    
                    <!-- Warning untuk total weight -->
                    <div class="weight-warning" style="margin-top: 1rem;">
                        <div id="weight_status" style="padding: 1rem; border-radius: 8px; background: #fff3cd; border: 2px solid #ffeaa7; color: #856404;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-balance-scale" style="font-size: 1.25rem;"></i>
                                <div>
                                    <strong>Total Bobot Periode Lain:</strong> 
                                    <span id="other_weights">{{ number_format($totalWeight, 2) }}</span>
                                    <span style="margin-left: 1rem;">|</span>
                                    <strong style="margin-left: 1rem;">Sisa Bobot:</strong> 
                                    <span id="remaining_weight">{{ number_format(1 - $totalWeight, 2) }}</span>
                                </div>
                            </div>
                            <small style="display: block; margin-top: 0.5rem; opacity: 0.8;">
                                Total semua bobot periode harus = 1.0 untuk perhitungan yang akurat
                            </small>
                        </div>
                    </div>
                @endif
            </div>

        <!-- EPC -->
        <div class="section-header">EPC</div>
        <div class="section-frame">
            <div class="table-responsive">
                <table class="table epc-table" id="epcTable">
                    <thead>
                        <tr>
                            <th rowspan="3" style="width:40px">No</th>
                            <th rowspan="3" style="width:180px;text-align:left">Description</th>
                            <th rowspan="2">Weight<br>Factor</th>
                            <th colspan="3">Cumulative Last</th>
                            <th colspan="2">This Period</th>
                            <th colspan="3">Cumulative Now</th>
                            <th rowspan="3" style="width:120px">Status</th>
                        </tr>
                        <tr>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                            <th>Plan</th><th>Actual</th>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                        </tr>
                        <tr class="formula-row">
                            <!-- Letters properly positioned under their columns -->
                            <th>A</th>
                            <th>B</th><th>C</th><th>C-B</th>
                            <th>D-B</th><th>E-C</th>
                            <th>D</th><th>E</th><th>E-D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $epcRows = optional($period)->epc ?? collect(); 
                            $regularEpcRows = $epcRows->where('is_total', false);
                            $totalEpcRow = $epcRows->where('is_total', true)->first();
                        @endphp
                        
                        {{-- Tampilkan baris regular --}}
                        @foreach($regularEpcRows as $i => $epc)
                        <tr class="epc-row">
                            <td>{{ ['','I','II','III','IV','V','VI','VII','VIII','IX','X'][$loop->iteration] ?? $loop->iteration }}</td>
                            <td><input type="text" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][description]" value="{{ $epc->description }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][weight_factor]" value="{{ $epc->weight_factor }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][cum_last_plan]" value="{{ $epc->cum_last_plan }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][cum_last_actual]" value="{{ $epc->cum_last_actual }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][cum_last_variance]" value="{{ $epc->cum_last_variance }}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][this_plan]" value="{{ $epc->this_plan }}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][this_actual]" value="{{ $epc->this_actual }}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][cum_now_plan]" value="{{ $epc->cum_now_plan }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][cum_now_actual]" value="{{ $epc->cum_now_actual }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][cum_now_variance]" value="{{ $epc->cum_now_variance }}" readonly style="background:#f8f9fa;"></td>
                            <td>
                                <select class="form-control form-control-sm" name="epc[{{ $loop->iteration }}][status]" {{ $locked ? 'disabled' : '' }}>
                                    <option value="on schedule" {{ $epc->status == 'on schedule' ? 'selected' : '' }}>On Schedule</option>
                                    <option value="delayed" {{ $epc->status == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                        
                        {{-- Jika tidak ada baris regular, buat baris kosong --}}
                        @if($regularEpcRows->isEmpty())
                        <tr class="epc-row">
                            <td>I</td>
                            <td><input type="text" class="form-control form-control-sm" name="epc[1][description]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][weight_factor]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_last_plan]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_last_actual]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_last_variance]" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][this_plan]" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][this_actual]" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_now_plan]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_now_actual]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_now_variance]" readonly style="background:#f8f9fa;"></td>
                            <td>
                                <select class="form-control form-control-sm" name="epc[1][status]" {{ $locked ? 'disabled' : '' }}>
                                    <option value="on schedule">On Schedule</option>
                                    <option value="delayed">Delayed</option>
                                </select>
                            </td>
                        </tr>
                        @endif
                        
                        {{-- Total Row (tanpa nomor, tidak bisa diedit) --}}
                        <tr class="epc-total-row" style="background:#f8f9fa; font-weight:bold;">
                            <td style="background:#e9ecef;"></td>
                            <td><input type="text" class="form-control form-control-sm" name="epc[total][description]" value="Total" readonly style="background:#e9ecef; font-weight:bold;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-weight" name="epc[total][weight_factor]" value="{{ $totalEpcRow ? $totalEpcRow->weight_factor : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-plan" name="epc[total][cum_last_plan]" value="{{ $totalEpcRow ? $totalEpcRow->cum_last_plan : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-actual" name="epc[total][cum_last_actual]" value="{{ $totalEpcRow ? $totalEpcRow->cum_last_actual : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-variance" name="epc[total][cum_last_variance]" value="{{ $totalEpcRow ? $totalEpcRow->cum_last_variance : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-this-plan" name="epc[total][this_plan]" value="{{ $totalEpcRow ? $totalEpcRow->this_plan : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-this-actual" name="epc[total][this_actual]" value="{{ $totalEpcRow ? $totalEpcRow->this_actual : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-plan" name="epc[total][cum_now_plan]" value="{{ $totalEpcRow ? $totalEpcRow->cum_now_plan : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-actual" name="epc[total][cum_now_actual]" value="{{ $totalEpcRow ? $totalEpcRow->cum_now_actual : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-variance" name="epc[total][cum_now_variance]" value="{{ $totalEpcRow ? $totalEpcRow->cum_now_variance : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td>
                                <select class="form-control form-control-sm epc-total-status" name="epc[total][status]" disabled style="background:#e9ecef;">
                                    <option value="on schedule" {{ ($totalEpcRow && $totalEpcRow->status == 'on schedule') ? 'selected' : '' }}>On Schedule</option>
                                    <option value="delayed" {{ ($totalEpcRow && $totalEpcRow->status == 'delayed') ? 'selected' : '' }}>Delayed</option>
                                </select>
                                <input type="hidden" name="epc[total][status]" value="{{ ($totalEpcRow && $totalEpcRow->status) ? $totalEpcRow->status : 'on schedule' }}">
                                <input type="hidden" name="epc[total][is_total]" value="1">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary" id="epcAddRow">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger" id="epcRemoveRow">Hapus Baris</button>
                </div>
            </div>
        </div>

        <!-- Engineering - Add category buttons to header and fix document buttons -->
        <div class="section-header">
            <span>Engineering</span>
            <div class="flex-right">
                <button type="button" class="btn-action-primary" id="engAddCategory">+ Tambah Kategori</button>
                <!-- <button type="button" class="btn-action-danger" id="engRemoveCategory">Hapus Kategori</button> -->
                <button type="button" class="btn-action-primary" id="engToggleButtons">Sembunyikan Tombol</button>
            </div>
        </div>

        <!-- Engineering section with updated Master position and smaller No column -->
        <div class="section-frame" id="engineeringWrap">
            <div class="table-responsive">
                <table class="table table-bordered" id="engineeringTable">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th colspan="6">Nomor Dokumen</th>
                            <th>Judul Dokumen / Gambar</th>
                            <th style="width:180px">Status Approve</th>
                        </tr>
                    </thead>
                    <tbody id="engineeringBody">
@php
    $engineerings = optional($period)->engineerings ?? collect();
    $kategoriNow = $subkategoriNow = $subNow = null;
    $globalDocIndex = 1;
@endphp
@if($engineerings->count())
    @foreach($engineerings as $eng)
        {{-- Kategori --}}
        @if($kategoriNow !== $eng->kategori)
            @php
                $kategoriNow = $eng->kategori;
            @endphp
            <tr class="kategori-row">
                <td></td>
                <td colspan="6" class="font-bold text-left bg-blue-50">
                    <input type="text" class="form-control form-control-sm kategori-input" name="engineering_kategori[]" value="{{ $eng->kategori }}" style="width:220px; display:inline-block;">
                    <span class="ml-2 text-xs text-gray-400">Kategori</span>
                </td>
                <td colspan="2" class="text-right">
                    <button type="button" class="btn-action-primary btn-master-add-sub mr-2">+ Tambah Subkategori</button>
                    <button type="button" class="btn-action-danger btn-master-remove-sub">Hapus Kategori</button>
                </td>
            </tr>
        @endif
        {{-- Subkategori --}}
        @if($subkategoriNow !== $eng->subkategori)
            @php
                $subkategoriNow = $eng->subkategori;
            @endphp
            <tr class="subkategori-row">
                <td class="font-bold text-blue-700 text-center">
                    {{ $eng->subkategori }}
                </td>
                <td colspan="6" class="font-bold text-blue-700 text-left bg-blue-100">
                    <input type="text" class="form-control form-control-sm subkategori-input" name="engineering_subkategori[]" value="{{ $eng->subkategori }}" style="width:220px; display:inline-block;">
                    <span class="ml-2 text-xs text-gray-400">Subkategori</span>
                </td>
                <td colspan="2" class="text-right">
                    <button type="button" class="btn-action-primary btn-eng-add-subsub mr-2">+ Tambah Sub</button>
                    <button type="button" class="btn-action-danger btn-eng-remove-subsub">Hapus Subkategori</button>
                </td>
            </tr>
        @endif
        {{-- Sub --}}
        @if($subNow !== $eng->sub)
            @php
                $subNow = $eng->sub;
                $noDoc = 1;
            @endphp
            <tr class="sub-row">
                <td class="text-center text-gray-700">
                    {{ $eng->sub }}
                </td>
                <td colspan="6" class="bg-blue-50 text-left">
                    <input type="text" class="form-control form-control-sm sub-input" name="engineering_sub[]" value="{{ $eng->sub }}" style="width:220px; display:inline-block;">
                    <span class="ml-2 text-xs text-gray-400">Sub</span>
                </td>
                <td colspan="2" class="text-right">
                    <button type="button" class="btn-action-primary btn-sub-add-doc mr-2">+ Tambah Dokumen</button>
                    <button type="button" class="btn-action-danger btn-remove-sub">Hapus Sub</button>
                </td>
            </tr>
        @endif
        {{-- Dokumen --}}
        <tr class="doc-row">
            <td class="text-center">{{ $noDoc ?? 1 }}</td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][doc_part1]" value="{{ $eng->doc_part1 }}"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][doc_part2]" value="{{ $eng->doc_part2 }}"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][doc_part3]" value="{{ $eng->doc_part3 }}"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][doc_part4]" value="{{ $eng->doc_part4 }}"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][doc_part5]" value="{{ $eng->doc_part5 }}"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][doc_part6]" value="{{ $eng->doc_part6 }}"></td>
            <td>
                <input type="text" class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][document_name]" value="{{ $eng->document_name }}">
            </td>
            <td>
                <select class="form-control form-control-sm" name="engineering[{{ $globalDocIndex }}][approval_status]">
                    <option value="ongoing" {{ $eng->approval_status == 'ongoing' ? 'selected' : '' }}>On Going</option>
                    <option value="done" {{ $eng->approval_status == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="delayed" {{ $eng->approval_status == 'delayed' ? 'selected' : '' }}>Delayed</option>
                </select>
                <input type="hidden" name="engineering[{{ $globalDocIndex }}][kategori]" value="{{ $eng->kategori }}">
                <input type="hidden" name="engineering[{{ $globalDocIndex }}][subkategori]" value="{{ $eng->subkategori }}">
                <input type="hidden" name="engineering[{{ $globalDocIndex }}][sub]" value="{{ $eng->sub }}">
            </td>
        </tr>
        @php 
            $noDoc = isset($noDoc) ? $noDoc + 1 : 2; 
            $globalDocIndex++;
        @endphp
    @endforeach
@endif
                    </tbody>
                </table>
                <div class="table-actions-bottom" id="engineeringButtons">
                    <button type="button" class="btn-action-primary" id="engAddDoc" style="height:34px;">+ Tambah Dokumen</button>
                    <button type="button" class="btn-action-danger" id="engDelDoc" style="height:34px;">Hapus Dokumen</button>
                </div>
            </div>
        </div>

        <!-- Procurement (two tables) -->
        <div class="section-header">Procurement</div>
        <div class="section-frame proc-section">
            <!-- material table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="materialTable">
                    <thead>
                        <tr>
                            <th style="width: 25%; min-width: 200px;">Material</th><th style="width: 20%; min-width: 150px;">Nomor PO</th><th>PO Terbit</th><th>Submit TPG</th><th>Approve TPG</th><th>Manufacture</th><th>FAT</th><th>Delivery</th><th>Material On Site</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $materials = optional($period)->procurementMaterials ?? collect(); @endphp
                        @foreach($materials as $i => $mat)
                        <tr class="mat-row">
                            <td><input type="text" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][material_name]" value="{{ $mat->material_name }}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][po_number]" value="{{ $mat->po_number }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][po_issued]" value="{{ $mat->po_issued }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][submit_tpg]" value="{{ $mat->submit_tpg }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][approve_tpg]" value="{{ $mat->approve_tpg }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][manufacture]" value="{{ $mat->manufacture }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][fat]" value="{{ $mat->fat }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][delivery]" value="{{ $mat->delivery }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[{{ $loop->iteration }}][material_on_site]" value="{{ $mat->material_on_site }}"></td>
                        </tr>
                        @endforeach
                        @if($materials->isEmpty())
                        <tr class="mat-row">
                            <td><input type="text" class="form-control form-control-sm" name="material[1][material_name]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="material[1][po_number]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][po_issued]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][submit_tpg]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][approve_tpg]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][manufacture]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][fat]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][delivery]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[1][material_on_site]"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary" id="matAddRow">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger" id="matRemoveRow">Hapus Baris</button>
                </div>
            </div>

            <!-- service table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="serviceTable">
                    <thead>
                        <tr>
                            <th>Jasa</th><th>Nomor PO</th><th>PO Terbit</th><th>Mobilisasi</th><th>Pekerjaan Lapangan</th><th>Pelaporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $services = optional($period)->procurementServices ?? collect(); @endphp
                        @foreach($services as $i => $svc)
                        <tr class="svc-row">
                            <td><input type="text" class="form-control form-control-sm" name="service[{{ $loop->iteration }}][service_name]" value="{{ $svc->service_name }}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="service[{{ $loop->iteration }}][po_number]" value="{{ $svc->po_number }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[{{ $loop->iteration }}][po_issued]" value="{{ $svc->po_issued }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[{{ $loop->iteration }}][mobilization]" value="{{ $svc->mobilization }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[{{ $loop->iteration }}][field_work]" value="{{ $svc->field_work }}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[{{ $loop->iteration }}][reporting]" value="{{ $svc->reporting }}"></td>
                        </tr>
                        @endforeach
                        @if($services->isEmpty())
                        <tr class="svc-row">
                            <td><input type="text" class="form-control form-control-sm" name="service[1][service_name]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="service[1][po_number]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[1][po_issued]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[1][mobilization]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[1][field_work]"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[1][reporting]"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary" id="svcAddRow">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger" id="svcRemoveRow">Hapus Baris</button>
                </div>
            </div>
        </div>

        <!-- Construction - Fixed column letter alignment -->
        <div class="section-header">Construction</div>
        <div class="section-frame">
            <div class="table-responsive">
                <table class="table table-bordered" id="constructionTable">
                    <thead>
                        <tr>
                            <th rowspan="3" style="width:40px">No</th>
                            <th rowspan="3" style="width:180px;text-align:left">Description</th>
                            <th rowspan="2">Weight<br>Factor</th>
                            <th colspan="3">Cumulative Last</th>
                            <th colspan="2">This Period</th>
                            <th colspan="3">Cumulative Now</th>
                            <th rowspan="3" style="width:120px">Status</th>
                        </tr>
                        <tr>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                            <th>Plan</th><th>Actual</th>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                        </tr>
                        <tr class="formula-row">
                            <!-- Letters properly positioned under their columns -->
                            <th>A</th>
                            <th>B</th><th>C</th><th>C-B</th>
                            <th>D-B</th><th>E-C</th>
                            <th>D</th><th>E</th><th>E-D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $conRows = optional($period)->constructions ?? collect(); 
                            $regularConRows = $conRows->where('is_total', false);
                            $totalConRow = $conRows->where('is_total', true)->first();
                        @endphp
                        
                        {{-- Tampilkan baris regular --}}
                        @foreach($regularConRows as $i => $con)
                        <tr class="con-row">
                            <td>{{ $loop->iteration }}</td>
                            <td><input type="text" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][description]" value="{{ $con->description }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][weight_factor]" value="{{ $con->weight_factor }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][cum_last_plan]" value="{{ $con->cum_last_plan }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][cum_last_actual]" value="{{ $con->cum_last_actual }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][cum_last_variance]" value="{{ $con->cum_last_variance }}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][this_plan]" value="{{ $con->this_plan }}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][this_actual]" value="{{ $con->this_actual }}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][cum_now_plan]" value="{{ $con->cum_now_plan }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][cum_now_actual]" value="{{ $con->cum_now_actual }}" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][cum_now_variance]" value="{{ $con->cum_now_variance }}" readonly style="background:#f8f9fa;"></td>
                            <td>
                                <select class="form-control form-control-sm" name="construction[{{ $loop->iteration }}][status]" {{ $locked ? 'disabled' : '' }}>
                                    <option value="on schedule" {{ $con->status == 'on schedule' ? 'selected' : '' }}>On Schedule</option>
                                    <option value="delayed" {{ $con->status == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                        
                        {{-- Jika tidak ada baris regular, buat baris kosong --}}
                        @if($regularConRows->isEmpty())
                        <tr class="con-row">
                            <td>1</td>
                            <td><input type="text" class="form-control form-control-sm" name="construction[1][description]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][weight_factor]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_last_plan]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_last_actual]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_last_variance]" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][this_plan]" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][this_actual]" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_now_plan]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_now_actual]" {{ $locked ? 'disabled' : '' }}></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_now_variance]" readonly style="background:#f8f9fa;"></td>
                            <td>
                                <select class="form-control form-control-sm" name="construction[1][status]" {{ $locked ? 'disabled' : '' }}>
                                    <option value="on schedule">On Schedule</option>
                                    <option value="delayed">Delayed</option>
                                </select>
                            </td>
                        </tr>
                        @endif
                        
                        {{-- Total Row (tanpa nomor, tidak bisa diedit) --}}
                        <tr class="con-total-row" style="background:#f8f9fa; font-weight:bold;">
                            <td style="background:#e9ecef;"></td>
                            <td><input type="text" class="form-control form-control-sm" name="construction[total][description]" value="Total" readonly style="background:#e9ecef; font-weight:bold;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-weight" name="construction[total][weight_factor]" value="{{ $totalConRow ? $totalConRow->weight_factor : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-plan" name="construction[total][cum_last_plan]" value="{{ $totalConRow ? $totalConRow->cum_last_plan : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-actual" name="construction[total][cum_last_actual]" value="{{ $totalConRow ? $totalConRow->cum_last_actual : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-variance" name="construction[total][cum_last_variance]" value="{{ $totalConRow ? $totalConRow->cum_last_variance : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-this-plan" name="construction[total][this_plan]" value="{{ $totalConRow ? $totalConRow->this_plan : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-this-actual" name="construction[total][this_actual]" value="{{ $totalConRow ? $totalConRow->this_actual : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-plan" name="construction[total][cum_now_plan]" value="{{ $totalConRow ? $totalConRow->cum_now_plan : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-actual" name="construction[total][cum_now_actual]" value="{{ $totalConRow ? $totalConRow->cum_now_actual : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-variance" name="construction[total][cum_now_variance]" value="{{ $totalConRow ? $totalConRow->cum_now_variance : '0.00' }}" readonly style="background:#e9ecef;"></td>
                            <td>
                                <select class="form-control form-control-sm con-total-status" name="construction[total][status]" disabled style="background:#e9ecef;">
                                    <option value="on schedule" {{ ($totalConRow && $totalConRow->status == 'on schedule') ? 'selected' : '' }}>On Schedule</option>
                                    <option value="delayed" {{ ($totalConRow && $totalConRow->status == 'delayed') ? 'selected' : '' }}>Delayed</option>
                                </select>
                                <input type="hidden" name="construction[total][status]" value="{{ ($totalConRow && $totalConRow->status) ? $totalConRow->status : 'on schedule' }}">
                                <input type="hidden" name="construction[total][is_total]" value="1">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary" id="conAddRow">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger" id="conRemoveRow">Hapus Baris</button>
                </div>
            </div>
        </div>

        <!-- Aktivitas Proyek (Dynamic Scopes) -->
        @php
            $activities = optional($period)->activities ?? collect();
            $scopes = $activities->pluck('scope')->unique()->sort()->values();
            $scopeLetters = $scopes->map(function($s){ return strtoupper($s); })->toArray();
            if (empty($scopeLetters)) {
                $scopeLetters = ['A']; // Default minimal Scope A
            }
            $lastScopeIdx = count($scopeLetters) ? max(array_map(function($s){ return ord($s)-65; }, $scopeLetters)) : 0;
        @endphp

        <div id="scopesContainer">
        @foreach($scopeLetters as $scope)
            <div class="scope-table-wrap" data-scope="{{ $scope }}">
                <div class="section-header">Scope {{ $scope }}</div>
                <div class="section-frame">
                    <div class="table-responsive">
                        <table class="table table-bordered activity-table" id="scopeTable{{ $scope }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lokasi</th>
                                    <th>Pencapaian Minggu Ini</th>
                                    <th>Progress (%)</th>
                                    <th>Rencana Minggu Depan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $acts = $activities->where('scope', $scope)->values(); @endphp
                                @foreach($acts as $i => $act)
                                <tr class="act-row">
                                    <td>{{ $i+1 }}</td>
                                    <td><input type="text" class="form-control form-control-sm" name="activity[{{ $scope }}][{{ $i+1 }}][location]" value="{{ $act->location }}"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[{{ $scope }}][{{ $i+1 }}][achievement_this_week]">{{ $act->achievement_this_week }}</textarea></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[{{ $scope }}][{{ $i+1 }}][progress]" value="{{ $act->progress }}"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[{{ $scope }}][{{ $i+1 }}][plan_next_week]">{{ $act->plan_next_week }}</textarea></td>
                                    <td>
                                        <select class="form-control form-control-sm" name="activity[{{ $scope }}][{{ $i+1 }}][status]">
                                            <option value="ongoing" {{ $act->status == 'ongoing' ? 'selected' : '' }}>On Going</option>
                                            <option value="done" {{ $act->status == 'done' ? 'selected' : '' }}>Done</option>
                                            <option value="delayed" {{ $act->status == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                                @if($acts->isEmpty())
                                <tr class="act-row">
                                    <td>1</td>
                                    <td><input type="text" class="form-control form-control-sm" name="activity[{{ $scope }}][1][location]"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[{{ $scope }}][1][achievement_this_week]"></textarea></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[{{ $scope }}][1][progress]"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[{{ $scope }}][1][plan_next_week]"></textarea></td>
                                    <td>
                                        <select class="form-control form-control-sm" name="activity[{{ $scope }}][1][status]">
                                            <option value="ongoing">On Going</option>
                                            <option value="done">Done</option>
                                            <option value="delayed">Delayed</option>
                                        </select>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="table-actions-bottom">
                            <button type="button" class="btn-action-primary scope-add-row" data-scope="{{ $scope }}">+ Tambah Baris</button>
                            <button type="button" class="btn-action-danger scope-remove-row" data-scope="{{ $scope }}">Hapus Baris</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>

        <div style="display:flex; align-items:center; gap:8px; margin:12px 0 24px 0;">
            <div style="flex:1"></div>
            <div>
                <button type="button" class="btn-action-primary" id="addScopeBtn">+ Tambah Scope</button>
                <button type="button" class="btn-action-danger" id="removeScopeBtn">Hapus Scope</button>
            </div>
        </div>

        <!-- Issue Table -->
        <div class="section-header" style="background:#009FDF;">Isu</div>
        <div class="section-frame">
            <div class="table-responsive">
                <table class="table table-bordered" id="issueTable">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th>Isu</th>
                            <th>Lokasi</th>
                            <th>Equipment/Part</th>
                            <th>Deskripsi</th>
                            <th style="width:120px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $issues = optional($period)->issues ?? collect(); @endphp
                        @foreach($issues as $i => $issue)
                        <tr class="issue-row">
                            <td>{{ $loop->iteration }}</td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[{{ $loop->iteration }}][name]" value="{{ $issue->name }}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[{{ $loop->iteration }}][location]" value="{{ $issue->location }}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[{{ $loop->iteration }}][equipment]" value="{{ $issue->equipment }}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[{{ $loop->iteration }}][description]" value="{{ $issue->description }}"></td>
                            <td>
                                <select class="form-control form-control-sm" name="issue[{{ $loop->iteration }}][status]">
                                    <option value="ongoing" {{ $issue->status == 'ongoing' ? 'selected' : '' }}>On Going</option>
                                    <option value="done" {{ $issue->status == 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                        
                        {{-- Default row jika kosong --}}
                        @if($issues->isEmpty())
                        <tr class="issue-row">
                            <td>1</td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][name]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][location]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][equipment]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][description]"></td>
                            <td>
                                <select class="form-control form-control-sm" name="issue[1][status]">
                                    <option value="ongoing">On Going</option>
                                    <option value="done">Done</option>
                                </select>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary" id="issueAddRow">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger" id="issueRemoveRow">Hapus Baris</button>
                </div>
            </div>
        </div>
    </form>

    <div id="submitModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                   <i class="fa fa-paper-plane"></i>
                  Konfirmasi Submit Periode
               </h3>
               <button type="button" class="modal-close" onclick="closeSubmitModal()">&times;</button>
            </div>
            <div class="modal-body">
                <h4>{{ $buttonText }} Periode</h4>
                <p>Apakah Anda yakin ingin {{ strtolower($buttonText) }} periode <strong>"{{ $period ? $period->name : '' }}"</strong>?</p>
                
                @if($user->isUser())
                    <div class="modal-warning">
                        <i class="fas fa-exclamation-triangle modal-warning-icon"></i>
                        <div class="modal-warning-text">
                            <strong>Perhatian:</strong> Setelah disubmit, periode akan dikirim untuk review dan Anda tidak dapat mengeditnya lagi sampai proses review selesai.
                        </div>
                    </div>
                @elseif($period && $period->canBeApprovedBy($user))
                    <div class="modal-warning">
                        <i class="fas fa-info-circle modal-warning-icon"></i>
                        <div class="modal-warning-text">
                            <strong>Info:</strong> Setelah diapprove, periode akan diteruskan ke tahap review berikutnya.
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeSubmitModal()">
                    <i class="fa fa-times"></i> 
                    Batal </button>
                <button type="button" id="confirmSubmitBtn" class="modal-btn modal-btn-submit" onclick="confirmSubmit()" id="confirmSubmitBtn">
                    <i class="fa fa-paper-plane"></i> Ya, Submit
                </button>
            </div>
        </div>
    </div>
</div>


{{-- AUDIT INFO PER PERIODE --}}
@if(isset($period) && $period)
@php
    $creator = optional($period->createdBy)->name ?? 'Sistem';
    $createdAt = $period->created_at
        ? $period->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB'
        : '-';
    $edited = $period->updated_by && $period->updated_at && !$period->updated_at->equalTo($period->created_at);
    $editor = optional($period->updatedBy)->name ?? 'Sistem';
    $updatedAt = $period->updated_at
        ? $period->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB'
        : null;
@endphp
<div style="max-width:1200px;margin:0 auto 24px auto;padding:12px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#334155;">
    <div style="margin-bottom:6px;">
        <strong>Periode dibuat oleh:</strong> {{ $creator }} pada {{ $createdAt }}
    </div>
    @if($edited)
        <div>
            <strong>Terakhir diedit oleh:</strong> {{ $editor }} pada {{ $updatedAt }}
        </div>
    @else
        <div>
            <strong>Status perubahan:</strong> Periode ini belum dilakukan perubahan sejak pembuatan.
        </div>
    @endif
</div>
@endif
@endsection
