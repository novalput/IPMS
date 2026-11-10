@extends('layouts.app')

@section('title', 'Tambah Proyek')

@section('content')

@vite([
    'resources/css/app/project-create.css',
    'resources/js/app/project-create.js'
])

<div class="form-create-project">
    <div class="page-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>Input Arsip Proyek Lampau</h2>
            <p style="color:#666; margin-top:5px;">Data langsung Approved & Completed.</p>
        </div>
        
        <a href="{{ route('completed-periods.index') }}" class="btn btn-secondary" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
            <i class="fa fa-check"></i> Selesai / Kembali ke Daftar
        </a>
    </div>

        <div class="form-content-wrapper">
        <form method="POST" action="{{ route('project.store-past') }}" id="projectForm" autocomplete="off">
            @csrf
            <input type="hidden" name="save_action" id="saveAction" value="finish">

            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                
                <button type="button" class="btn-submit" id="btnSaveNext" style="background: #00AEEF; border:none;">
                    <i class="fa fa-plus-circle"></i> Simpan & Tambah Periode Berikutnya
                </button>

                <button type="button" class="btn-submit" id="btnSaveFinish" style="background: #28a745; border:none;">
                    <i class="fa fa-check-circle"></i> Simpan & Selesai
                </button>

            </div>
            <!-- Nama Proyek row -->
            <div class="project-header">
                <label class="section-label">Nama Proyek</label>
                <input id="projectInput" name="project_name" type="text" class="form-control" placeholder="Masukkan nama proyek baru" style="width:420px;"/>
                <div class="dropdown-wrap" style="margin-left:4px;">
                    <select class="dropdown-select" id="projectSelect" name="existing_project_id">
                        <option value="">-- Pilih Proyek Existing --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <span class="dropdown-caret"><i class="fa fa-caret-down"></i></span>
                </div>
                <button type="button" class="btn-action-primary" id="copyProjectTemplateBtn" style="margin-left:8px;">Copy Template</button>
            </div>

            <div class="period-header">
                <label class="section-label">Periode</label>
                <input id="periodInput" name="period_name" type="text" class="form-control" placeholder="Masukkan nama periode baru" style="width:420px;" />
                <div class="dropdown-wrap" style="margin-left:4px;">
                    <select class="dropdown-select" id="periodSelect" name="template_source_id"> <option value="">-- Pilih Periode Existing --</option>
                    </select>
                    <span class="dropdown-caret"><i class="fa fa-caret-down"></i></span>
                </div>
                <button type="button" class="btn-action-primary" id="copyPeriodTemplateBtn" style="margin-left:8px;">Copy Template</button>
            </div>

            <div class="period-range">
                <label style="min-width:120px"></label>
                <input type="date" id="startDate" name="start_date" class="form-control" style="width:200px;">
                <input type="date" id="endDate" name="end_date" class="form-control" style="width:200px;">
            </div>

            <div class="project-header" style="margin-top: 15px; background: #fff8e1; border: 1px solid #ffe0b2; padding: 15px; border-radius: 8px;">
                <label class="section-label" style="color: #d35400;">Tanggal Penyelesaian (Cut-Off)</label>
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <input type="datetime-local" id="pastCompletedAt" name="past_completed_at" class="form-control" required style="width:250px;">
                    <small style="color: #d35400;">* Tanggal ini akan tercatat sebagai tanggal approval dan tanggal tracking.</small>
                </div>
            </div>

            <div id="templatePreview" class="mt-4"></div>

            <input type="hidden" name="current_step" id="currentStep" value="1">
            <input type="hidden" name="created_period_name" id="createdPeriodName" value="">
            <input type="hidden" name="created_period_from" id="createdPeriodFrom" value="">
            <input type="hidden" name="created_period_to" id="createdPeriodTo" value="">

            <div class="section-header">Deskripsi Proyek</div>
            <div class="form-section">
                <textarea name="description" class="form-control text-description" placeholder="Masukkan deskripsi proyek">{{ old('description') }}</textarea>
            </div>

            <!-- TAMBAHKAN FORM PROGRESS DI SINI -->
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
                            value="{{ old('progress_percentage', 100) }}"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e6ed; border-radius: 8px; font-size: 1rem;"
                            required
                            oninput="calculateProgress()"
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
                            value="{{ old('weight', 1) }}"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e0e6ed; border-radius: 8px; font-size: 1rem;"
                            required
                            oninput="calculateProgress()"
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
                            value="0%"
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
                            <span id="preview_percentage" style="font-size: 1.25rem; font-weight: 700; color: #00AEEF;">0%</span>
                        </div>
                        <div style="height: 12px; background: #e9ecef; border-radius: 10px; overflow: hidden;">
                            <div id="preview_bar" style="height: 100%; background: linear-gradient(90deg, #00AEEF 0%, #004ADF 100%); width: 0%; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                    <div style="font-size: 0.9rem; color: #7f8c8d;">
                        <strong>Kalkulasi:</strong> 
                        <span id="calc_formula">0% × 0 = 0%</span>
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
                    >{{ old('progress_notes') }}</textarea>
                    <small style="color: #7f8c8d; font-size: 0.85rem;">Catatan opsional untuk dokumentasi progress</small>
                </div>
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
                            <tr class="epc-row">
                                <td>I</td>
                                <td><input type="text" class="form-control form-control-sm" name="epc[1][description]" value="Engineering"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][weight_factor]" value="0.00"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_last_plan]" value="100.00"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_last_actual]" value="100.00"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_last_variance]" value="0.00" readonly style="background:#f8f9fa;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][this_plan]" value="0.00" readonly style="background:#f8f9fa;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][this_actual]" value="0.00" readonly style="background:#f8f9fa;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_now_plan]" value="100.00"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_now_actual]" value="100.00"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[1][cum_now_variance]" value="0.00" readonly style="background:#f8f9fa;"></td>
                                <td>
                                    <select class="form-control form-control-sm" name="epc[1][status]">
                                        <option value="on schedule">On Schedule</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                </td>
                            </tr>
                            <!-- Total Row -->
                            <tr class="epc-total-row" style="background:#f8f9fa; font-weight:bold;">
                                <td></td>
                                <td><input type="text" class="form-control form-control-sm" name="epc[total][description]" value="Total" readonly style="background:#e9ecef; font-weight:bold;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-weight" name="epc[total][weight_factor]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-plan" name="epc[total][cum_last_plan]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-actual" name="epc[total][cum_last_actual]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-variance" name="epc[total][cum_last_variance]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-this-plan" name="epc[total][this_plan]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-this-actual" name="epc[total][this_actual]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-plan" name="epc[total][cum_now_plan]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-actual" name="epc[total][cum_now_actual]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-variance" name="epc[total][cum_now_variance]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td>
                                    <select class="form-control form-control-sm epc-total-status" name="epc[total][status]" disabled style="background:#e9ecef;">
                                        <option value="on schedule">On Schedule</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                    <input type="hidden" name="construction[total][status]" value="on schedule">
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
                            <!-- Mulai kosong, user harus tambah kategori/subkategori/sub/dokumen via tombol -->
                        </tbody>
                    </table>
                    <div class="table-actions-bottom" id="engineeringButtons">
                        <!-- <button type="button" class="btn-action-primary" id="engAddCategory" style="height:34px;">+ Tambah Kategori</button>
                        <button type="button" class="btn-action-danger" id="engRemoveCategory" style="height:34px;">Hapus Kategori</button> -->
                        <button type="button" class="btn-action-primary" id="engAddDoc" style="height:34px;">+ Tambah Dokumen</button>
                        <button type="button" class="btn-action-danger" id="engDelDoc" style="height:34px;">Hapus Dokumen</button>
                        <!-- <button type="button" class="btn-action-primary" id="engToggleButtons" style="height:34px;">Sembunyikan Tombol</button> -->
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
                            <tr class="svc-row">
                                <td><input type="text" class="form-control form-control-sm" name="service[1][service_name]"></td>
                                <td><input type="text" class="form-control form-control-sm" name="service[1][po_number]"></td>
                                <td><input type="date" class="form-control form-control-sm" name="service[1][po_issued]"></td>
                                <td><input type="date" class="form-control form-control-sm" name="service[1][mobilization]"></td>
                                <td><input type="date" class="form-control form-control-sm" name="service[1][field_work]"></td>
                                <td><input type="date" class="form-control form-control-sm" name="service[1][reporting]"></td>
                            </tr>
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
                            <tr class="con-row">
                                <td>1</td>
                                <td><input type="text" class="form-control form-control-sm" name="construction[1][description]"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][weight_factor]"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_last_plan]"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_last_actual]"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_last_variance]" readonly style="background:#f8f9fa;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][this_plan]" readonly style="background:#f8f9fa;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][this_actual]" readonly style="background:#f8f9fa;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_now_plan]"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_now_actual]"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[1][cum_now_variance]" readonly style="background:#f8f9fa;"></td>
                                <td>
                                    <select class="form-control form-control-sm" name="construction[1][status]">
                                        <option value="on schedule">On Schedule</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                </td>
                            </tr>
                            <!-- Total Row -->
                            <tr class="con-total-row" style="background:#f8f9fa; font-weight:bold;">
                                <td></td>
                                <td><input type="text" class="form-control form-control-sm" name="construction[total][description]" value="Total" readonly style="background:#e9ecef; font-weight:bold;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-weight" name="construction[total][weight_factor]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-plan" name="construction[total][cum_last_plan]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-actual" name="construction[total][cum_last_actual]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-variance" name="construction[total][cum_last_variance]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-this-plan" name="construction[total][this_plan]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-this-actual" name="construction[total][this_actual]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-plan" name="construction[total][cum_now_plan]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-actual" name="construction[total][cum_now_actual]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-variance" name="construction[total][cum_now_variance]" value="0.00" readonly style="background:#e9ecef;"></td>
                                <td>
                                    <select class="form-control form-control-sm con-total-status" name="construction[total][status]" disabled style="background:#e9ecef;">
                                        <option value="on schedule">On Schedule</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                    <input type="hidden" name="construction[total][status]" value="on schedule">
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

            <!-- Aktivitas Proyek (Scope A as default table) -->
            <div class="section-header">Aktivitas Proyek</div>
            <div class="section-frame">
                <div class="table-responsive">
                    <table class="table table-bordered activity-table" id="scopeTableA">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lokasi</th>
                                <th>Capaian Minggu Ini</th>
                                <th>Progress</th>
                                <th>Rencana Minggu Depan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="act-row">
                                <td>1</td>
                                <td><input type="text" class="form-control form-control-sm" name="activity[A][1][location]"></td>
                                <td><textarea class="form-control form-control-sm" name="activity[A][1][achievement_this_week]"></textarea></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[A][1][progress]"></td>
                                <td><textarea class="form-control form-control-sm" name="activity[A][1][plan_next_week]"></textarea></td>
                                <td>
                                    <select class="form-control form-control-sm" name="activity[A][1][status]">
                                        <option value="ongoing">On Going</option>
                                        <option value="done">Done</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="table-actions-bottom">
                        <button type="button" class="btn-action-primary scope-add-row" data-scope="A">+ Tambah Baris</button>
                        <button type="button" class="btn-action-danger scope-remove-row" data-scope="A">Hapus Baris</button>
                    </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <div style="flex:1"></div>
                <div>
                    <button type="button" class="btn-action-primary" id="addScopeBtn">+ Tambah Scope</button>
                    <button type="button" class="btn-action-danger" id="removeScopeBtn">Hapus Scope</button>
                </div>
            </div>
            <div id="scopesContainer"></div>

            <!-- <div class="actions-row" style="margin-top:12px;">
                <a href="{{ route('dashboard') }}" class="btn btn-action btn-secondary-action">Return</a>
                <button type="button" id="submitAsReviewer" class="btn btn-action">Approval</button>
            </div> -->
            <div id="submitModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            <i class="fa fa-paper-plane"></i>
                            Konfirmasi Submit Proyek
                        </h3>
                        <button type="button" class="modal-close" onclick="closeSubmitModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h4>Apakah Anda yakin ingin submit proyek ini?</h4>
                        <p>Setelah di-submit, proyek akan dikirim untuk review dan Anda tidak akan dapat mengedit data lagi sampai proyek dikembalikan oleh reviewer.</p>
                        
                        <div class="modal-warning">
                            <i class="fa fa-exclamation-triangle modal-warning-icon"></i>
                            <div class="modal-warning-text">
                                <strong>Perhatian:</strong> Pastikan semua data sudah benar dan lengkap sebelum melakukan submit. Perubahan setelah submit hanya bisa dilakukan setelah proyek dikembalikan oleh reviewer.
                            </div>
                        </div>
                        
                        <p><strong>Data yang akan di-submit meliputi:</strong></p>
                        <ul style="margin: 0; padding-left: 20px; color: #666;">
                            <li>Informasi proyek dan periode</li>
                            <li>Deskripsi proyek</li>
                            <li>Data EPC, Engineering, Procurement, dan Construction</li>
                            <li>Aktivitas proyek semua scope</li>
                            <li>Data isu proyek</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-cancel" onclick="closeSubmitModal()">
                            <i class="fa fa-times"></i>
                            Batal
                        </button>
                        <button type="button" class="modal-btn modal-btn-submit" onclick="confirmSubmit()" id="confirmSubmitBtn">
                            <i class="fa fa-paper-plane"></i>
                            Ya, Submit Proyek
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="modalNext" class="modal">
    <div class="modal-content">
        <div class="modal-header" style="background: #00AEEF; color: white;">
            <h3 class="modal-title">
                <i class="fa fa-plus-circle"></i>
                Simpan & Lanjut Periode Berikutnya
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modalNext')">&times;</button>
        </div>
        <div class="modal-body">
            <h4>Konfirmasi Penyimpanan</h4>
            <p>Anda akan menyimpan periode ini dan <strong>melanjutkan input periode berikutnya</strong> untuk proyek yang sama.</p>
            
            <div class="modal-warning" style="background: #e3f2fd; border-left: 4px solid #00AEEF; padding: 10px; margin: 15px 0;">
                <i class="fa fa-info-circle" style="color: #00AEEF;"></i>
                <div class="modal-warning-text" style="color: #0d47a1;">
                    <strong>Info:</strong> Status periode akan langsung set <strong>APPROVED</strong> dan halaman akan di-reset otomatis untuk input selanjutnya.
                </div>
            </div>
            
            <p>Pastikan tanggal Cut-Off sudah benar.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('modalNext')">
                Batal
            </button>
            <button type="button" class="modal-btn" style="background: #00AEEF; color: white;" onclick="confirmSubmit('next')">
                <i class="fa fa-paper-plane"></i>
                Ya, Simpan & Lanjut
            </button>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modalFinish" class="modal">
    <div class="modal-content">
        <div class="modal-header" style="background: #28a745; color: white;">
            <h3 class="modal-title">
                <i class="fa fa-check-circle"></i>
                Simpan & Selesai (Arsip)
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modalFinish')">&times;</button>
        </div>
        <div class="modal-body">
            <h4>Konfirmasi Arsip Final</h4>
            <p>Anda akan menyimpan periode ini dan <strong>menyelesaikan proses input</strong> untuk proyek ini.</p>
            
            <div class="modal-warning" style="background: #e8f5e9; border-left: 4px solid #28a745; padding: 10px; margin: 15px 0;">
                <i class="fa fa-check" style="color: #28a745;"></i>
                <div class="modal-warning-text" style="color: #1b5e20;">
                    <strong>Info:</strong> Anda akan diarahkan kembali ke menu <strong>Completed Periods</strong>. Data tidak dapat diedit lagi kecuali diaktifkan ulang oleh Admin.
                </div>
            </div>
            
            <p>Apakah semua data periode untuk proyek ini sudah lengkap?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('modalFinish')">
                Batal
            </button>
            <button type="button" class="modal-btn" style="background: #28a745; color: white;" onclick="confirmSubmit('finish')">
                <i class="fa fa-archive"></i>
                Ya, Simpan & Selesai
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('projectForm');
        const saveActionInput = document.getElementById('saveAction');
        const pastDateInput = document.getElementById('pastCompletedAt');

        // Fungsi Membuka Modal
        window.openModal = function(modalId) {
            // Validasi Tanggal Dulu sebelum buka modal
            if(!pastDateInput.value){
                alert('Harap isi Tanggal Penyelesaian (Cut-Off) terlebih dahulu!');
                pastDateInput.focus();
                // Highlight input merah
                pastDateInput.style.border = "2px solid #dc3545";
                return;
            }
            pastDateInput.style.border = ""; // Reset border

            document.getElementById(modalId).style.display = 'block';
            document.body.style.overflow = 'hidden'; // Lock scroll
        };

        // Fungsi Tutup Modal
        window.closeModal = function(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scroll
        };

        // Fungsi Eksekusi Submit dari dalam Modal
        window.confirmSubmit = function(actionType) {
            // Set action hidden input
            saveActionInput.value = actionType;
            
            // Visual feedback pada tombol di dalam modal
            const activeModal = actionType === 'next' ? 'modalNext' : 'modalFinish';
            const btn = document.querySelector(`#${activeModal} .modal-footer button:last-child`);
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
            
            form.submit();
        };

        // Event Listener Tombol Utama (Memicu Modal)
        const btnNext = document.getElementById('btnSaveNext');
        if(btnNext) {
            btnNext.addEventListener('click', function(e) {
                e.preventDefault();
                openModal('modalNext');
            });
        }

        const btnFinish = document.getElementById('btnSaveFinish');
        if(btnFinish) {
            btnFinish.addEventListener('click', function(e) {
                e.preventDefault();
                openModal('modalFinish');
            });
        }

        // Tutup modal jika klik di luar area konten (Overlay click)
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // --- Logic Auto-Select Project (Tetap Dipertahankan) ---
        const urlParams = new URLSearchParams(window.location.search);
        const preselectedProjectId = urlParams.get('project_id');

        if (preselectedProjectId) {
            const projectSelect = document.getElementById('projectSelect');
            if (projectSelect) {
                projectSelect.value = preselectedProjectId;
                projectSelect.dispatchEvent(new Event('change'));

                const headerProject = document.querySelector('.project-header');
                if(headerProject) {
                    headerProject.style.border = "2px solid #28a745";
                    let msg = document.createElement('div');
                    msg.innerHTML = '<small style="color:#28a745; font-weight:bold; display:block; margin-bottom:5px;"><i class="fa fa-info-circle"></i> Proyek otomatis terpilih. Silakan input periode berikutnya.</small>';
                    headerProject.insertBefore(msg, headerProject.firstChild);
                }
            }
        }
    });
</script>
@endsection