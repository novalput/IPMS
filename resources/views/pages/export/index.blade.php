@extends('layouts.app')

@section('title', 'Export Data Proyek - Integrated Project Management System')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<style>
    .export-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 120px);
        padding: 20px;
        max-width: 100%;
    }

    .export-left {
        width: 40%;
        min-width: 480px;
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow-y: auto;
        height: fit-content;
        max-height: 100%;
    }

    .export-right {
        flex: 1;
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow-y: auto;
    }

    .export-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .export-subtitle {
        color: #666;
        font-size: 14px;
        margin-bottom: 24px;
    }

    .form-section {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #333;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e0e6ed;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #00AEEF;
        box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.1);
    }

    /* Button-style checkboxes for sections */
    .sections-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 8px;
    }

    .section-button {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        min-height: 50px;
    }

    .section-button:hover {
        border-color: #00AEEF;
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 174, 239, 0.15);
    }

    .section-button.active {
        border-color: #00AEEF;
        background-color: rgba(0, 174, 239, 0.1);
        box-shadow: 0 0 0 1px rgba(0, 174, 239, 0.2);
    }

    .section-button input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #00AEEF;
        cursor: pointer;
    }

    .section-button .section-info {
        flex: 1;
    }

    .section-button .section-name {
        font-weight: 600;
        color: #333;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .section-button .section-desc {
        font-size: 11px;
        color: #666;
        line-height: 1.3;
    }

    .periods-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #e0e6ed;
        border-radius: 6px;
        padding: 8px;
        background: #f8f9fa;
    }

    .period-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        margin-bottom: 6px;
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .period-item:hover {
        border-color: #00AEEF;
        box-shadow: 0 1px 4px rgba(0, 174, 239, 0.1);
    }

    .period-item.selected {
        border-color: #00AEEF;
        background-color: rgba(0, 174, 239, 0.05);
    }

    .period-info {
        flex: 1;
    }

    .period-name {
        font-weight: 500;
        color: #333;
        margin-bottom: 2px;
        font-size: 13px;
    }

    .period-date {
        font-size: 11px;
        color: #666;
    }

    .radio-group {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 2px solid #e9ecef;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
    }

    .radio-item:hover {
        border-color: #00AEEF;
        background-color: #f8f9fa;
    }

    .radio-item.checked {
        border-color: #00AEEF;
        background-color: rgba(0, 174, 239, 0.1);
    }

    .radio-item input[type="radio"] {
        width: 16px;
        height: 16px;
        accent-color: #00AEEF;
    }

    .export-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-export {
        flex: 1;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-export-docx {
        background: #2B5CE6;
        color: #fff;
    }

    .btn-export-docx:hover {
        background: #1e40af;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 92, 230, 0.3);
    }

    .btn-export-pdf {
        background: #DC2626;
        color: #fff;
    }

    .btn-export-pdf:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-back {
        background: #6c757d;
        color: #fff;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        transition: background 0.2s;
        margin-bottom: 16px;
    }

    .btn-back:hover {
        background: #5a6268;
        text-decoration: none;
        color: #fff;
    }

    .alert {
        padding: 10px 12px;
        border-radius: 4px;
        margin-bottom: 16px;
        font-size: 12px;
    }

    .alert-warning {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
    }

    /* Preview Area */
    .preview-header {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
    }

    .preview-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preview-content {
        height: calc(100vh - 220px);
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #f8f9fa;
        position: relative;
    }

    .preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #666;
        text-align: center;
        font-size: 16px;
    }

    .preview-placeholder i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 16px;
    }

    .preview-document {
        background: #fff;
        margin: 20px;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        line-height: 1.4;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-content {
        background: #fff;
        padding: 32px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #e9ecef;
        border-left: 3px solid #00AEEF;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 1200px) {
        .export-container {
            flex-direction: column;
            height: auto;
        }
        
        .export-left {
            width: 100%;
            min-width: auto;
        }
        
        .preview-content {
            height: 400px;
        }
    }
</style>

<div class="export-container">
    <!-- Left Panel - Form -->
    <div class="export-left">
        <a href="{{ route('dashboard') }}" class="btn-back">
            <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        <div class="export-title">
            <i class="fa fa-download" style="color: #00AEEF;"></i>
            Export Data Proyek
        </div>
        <div class="export-subtitle">
            Ekspor data proyek dalam format Word (.docx) atau PDF sesuai kebutuhan Anda
        </div>

        <!-- FORM UTAMA - Ganti action ke route yang benar -->
        <form id="exportForm" action="{{ route('export.download') }}" method="POST">
            @csrf
            
            <!-- Project Selection -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-project-diagram"></i>
                    Pilih Proyek
                </div>
                
                <div class="form-group">
                    <label for="project_id" class="form-label">Proyek *</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Period Selection -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-calendar"></i>
                    Pilih Periode
                </div>
                
                <div class="alert alert-warning">
                    <i class="fa fa-info-circle"></i>
                    Pilih satu atau lebih periode yang ingin diekspor.
                </div>
                
                <div id="periodsContainer" class="periods-container" style="display: none;">
                    <!-- Periods will be loaded here -->
                </div>
            </div>

            <!-- Template Type -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-file-alt"></i>
                    Jenis Laporan
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pilih jenis laporan *</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" name="template_type" id="mingguan" value="mingguan" required>
                            <label for="mingguan">Laporan Mingguan</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" name="template_type" id="bulanan" value="bulanan" required>
                            <label for="bulanan">Laporan Bulanan</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="template_name" class="form-label">Nama Template (Opsional)</label>
                    <input type="text" name="template_name" id="template_name" class="form-control" 
                           placeholder="Contoh: Laporan Progress Mingguan Februari 2024">
                    <small style="color: #666; font-size: 11px;">Jika dikosongkan, akan menggunakan nama default berdasarkan proyek dan jenis laporan</small>
                </div>
            </div>

            <!-- Data Sections -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-list-check"></i>
                    Pilih Data yang Diekspor
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pilih bagian data yang ingin disertakan *</label>
                    <button type="button" id="btnToggleAllSections" style="font-size: 12px; padding: 4px 10px; border: 1px solid #00AEEF; background: #fff; color: #00AEEF; border-radius: 4px; cursor: pointer; transition: all 0.2s;">
                        Pilih Semua
                    </button>
                    <div class="sections-grid">
                        <div class="section-button" data-section="epc">
                            <input type="checkbox" name="sections[]" id="epc" value="epc">
                            <div class="section-info">
                                <div class="section-name">EPC Progress</div>
                                <div class="section-desc">Engineering, Procurement & Construction progress dengan semua kolom data</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="engineering">
                            <input type="checkbox" name="sections[]" id="engineering" value="engineering">
                            <div class="section-info">
                                <div class="section-name">Engineering Documents</div>
                                <div class="section-desc">Dokumen engineering dengan kategori, nomor dokumen, dan status approval</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="procurement_material">
                            <input type="checkbox" name="sections[]" id="procurement_material" value="procurement_material">
                            <div class="section-info">
                                <div class="section-name">Procurement Material</div>
                                <div class="section-desc">Material procurement: PO, TPG, Manufacturing, FAT, Delivery, On Site</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="procurement_service">
                            <input type="checkbox" name="sections[]" id="procurement_service" value="procurement_service">
                            <div class="section-info">
                                <div class="section-name">Procurement Service</div>
                                <div class="section-desc">Service procurement: PO, Mobilisasi, Pekerjaan Lapangan, Pelaporan</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="construction">
                            <input type="checkbox" name="sections[]" id="construction" value="construction">
                            <div class="section-info">
                                <div class="section-name">Construction Progress</div>
                                <div class="section-desc">Progress konstruksi dengan bobot, rencana, aktual, dan variance</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="activities">
                            <input type="checkbox" name="sections[]" id="activities" value="activities">
                            <div class="section-info">
                                <div class="section-name">Activities</div>
                                <div class="section-desc">Aktivitas proyek per scope: lokasi, pencapaian, progress, rencana</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="issues">
                            <input type="checkbox" name="sections[]" id="issues" value="issues">
                            <div class="section-info">
                                <div class="section-name">Issues</div>
                                <div class="section-desc">Daftar isu: nama, lokasi, equipment, deskripsi, status</div>
                            </div>
                        </div>
                        
                        <div class="section-button" data-section="documentation">
                            <input type="checkbox" name="sections[]" id="documentation" value="documentation">
                            <div class="section-info">
                                <div class="section-name">Dokumentasi Foto</div>
                                <div class="section-desc">Foto dokumentasi dengan judul dan deskripsi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="export-actions">
                <button type="submit" name="format" value="docx" class="btn-export btn-export-docx">
                    <i class="fa fa-file-word"></i>
                    Export ke Word
                </button>
                <button type="submit" name="format" value="pdf" class="btn-export btn-export-pdf">
                    <i class="fa fa-file-pdf"></i>
                    Export ke PDF
                </button>
            </div>
        </form>
        </div>

    <!-- Right Panel - Preview -->
    <div class="export-right">
        <div class="preview-header">
            <div class="preview-title">
                <i class="fa fa-eye" style="color: #00AEEF;"></i>
                Preview Dokumen
            </div>
        </div>
        
        <div class="preview-content" id="previewContent">
            <div class="preview-placeholder">
                <i class="fa fa-file-alt"></i>
                <h4>Preview Laporan</h4>
                <p>Silakan pilih proyek, periode, dan bagian data<br>untuk melihat preview dokumen yang akan diekspor</p>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <h3>Memproses Export...</h3>
        <p>Mohon tunggu, file sedang dibuat</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const periodsContainer = document.getElementById('periodsContainer');
    const exportForm = document.getElementById('exportForm');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const previewContent = document.getElementById('previewContent');
    const hutamaLogoOptions = document.getElementById('hutamaLogoOptions');

    // Load periods when project changes
    projectSelect.addEventListener('change', function() {
        const projectId = this.value;
        
        if (!projectId) {
            periodsContainer.style.display = 'none';
            periodsContainer.innerHTML = '';
            updatePreview();
            return;
        }

        // Fetch periods
        fetch(`/export/periods?project_id=${projectId}`)
            .then(response => response.json())
            .then(periods => {
                periodsContainer.innerHTML = '';
                
                if (periods.length === 0) {
                    periodsContainer.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Tidak ada periode ditemukan untuk proyek ini.</p>';
                } else {
                    periods.forEach(period => {
                        const periodItem = document.createElement('div');
                        periodItem.className = 'period-item';
                        periodItem.innerHTML = `
                            <input type="checkbox" name="periods[]" value="${period.id}" id="period_${period.id}">
                            <div class="period-info">
                                <div class="period-name">${period.name}</div>
                                <div class="period-date">${period.start_date || 'N/A'} - ${period.end_date || 'N/A'}</div>
                            </div>
                        `;
                        
                        periodItem.addEventListener('click', function(e) {
                            if (e.target.type !== 'checkbox') {
                                const checkbox = this.querySelector('input[type="checkbox"]');
                                checkbox.checked = !checkbox.checked;
                                this.classList.toggle('selected', checkbox.checked);
                                updatePreview();
                            } else {
                                this.classList.toggle('selected', e.target.checked);
                                updatePreview();
                            }
                        });
                        
                        periodsContainer.appendChild(periodItem);
                    });
                }
                
                periodsContainer.style.display = 'block';
                updatePreview();
            })
            .catch(error => {
                console.error('Error fetching periods:', error);
                periodsContainer.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;">Gagal memuat periode. Silakan coba lagi.</p>';
                periodsContainer.style.display = 'block';
            });
    });

    // Handle section button clicks
    document.addEventListener('click', function(e) {
        const sectionButton = e.target.closest('.section-button');
        if (sectionButton) {
            const checkbox = sectionButton.querySelector('input[type="checkbox"]');
            if (e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }
            sectionButton.classList.toggle('active', checkbox.checked);
            updatePreview();
        }
        
        if (e.target.type === 'radio') {
            const allRadioItems = document.querySelectorAll('.radio-item');
            allRadioItems.forEach(item => item.classList.remove('checked'));
            
            const checkedItem = e.target.closest('.radio-item');
            if (checkedItem) {
                checkedItem.classList.add('checked');
            }
            updatePreview();
        }
    });

    // Handle checkbox changes in section buttons
    document.addEventListener('change', function(e) {
        if (e.target.name === 'sections[]') {
            const sectionButton = e.target.closest('.section-button');
            if (sectionButton) {
                sectionButton.classList.toggle('active', e.target.checked);
            }
            updatePreview();
        }
        
        if (e.target.name === 'periods[]') {
            updatePreview();
        }
        
        if (e.target.name === 'template_type') {
            updatePreview();
        }
    });

    // Update preview function
    function updatePreview() {
        const projectId = document.getElementById('project_id').value;
        const selectedPeriods = Array.from(document.querySelectorAll('input[name="periods[]"]:checked')).map(cb => cb.value);
        const selectedSections = Array.from(document.querySelectorAll('input[name="sections[]"]:checked')).map(cb => cb.value);
        const templateType = document.querySelector('input[name="template_type"]:checked')?.value;
        const templateName = document.getElementById('template_name').value;

        if (!projectId || selectedPeriods.length === 0) {
            previewContent.innerHTML = `
                <div class="preview-placeholder">
                    <i class="fa fa-file-alt"></i>
                    <h4>Preview Laporan</h4>
                    <p>Silakan pilih proyek dan periode<br>untuk melihat preview dokumen yang akan diekspor</p>
                </div>
            `;
            return;
        }

        if (selectedSections.length === 0) {
            previewContent.innerHTML = `
                <div class="preview-placeholder">
                    <i class="fa fa-exclamation-circle"></i>
                    <h4>Pilih Data</h4>
                    <p>Pilih minimal satu bagian data<br>yang ingin disertakan dalam laporan</p>
                </div>
            `;
            return;
        }

        // Show loading
        previewContent.innerHTML = `
            <div class="preview-placeholder">
                <div class="loading-spinner"></div>
                <h4>Memuat Preview...</h4>
                <p>Mohon tunggu sebentar</p>
            </div>
        `;

        // Fetch preview
        const formData = new FormData();
        formData.append('project_id', projectId);
        selectedPeriods.forEach(id => formData.append('periods[]', id));
        selectedSections.forEach(section => formData.append('sections[]', section));
        if (templateType) formData.append('template_type', templateType);
        if (templateName) formData.append('template_name', templateName);

        fetch('/export/preview', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            previewContent.innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error fetching preview:', error);
            previewContent.innerHTML = `
                <div class="preview-placeholder">
                    <i class="fa fa-exclamation-triangle"></i>
                    <h4>Error</h4>
                    <p>Gagal memuat preview.<br>Silakan coba lagi.</p>
                </div>
            `;
        });
    }

    // Handle form submission
    exportForm.addEventListener('submit', function(e) {
        // Validate form
        const projectId = document.getElementById('project_id').value;
        const selectedPeriods = document.querySelectorAll('input[name="periods[]"]:checked');
        const templateType = document.querySelector('input[name="template_type"]:checked');
        const selectedSections = document.querySelectorAll('input[name="sections[]"]:checked');

        if (!projectId) {
            e.preventDefault();
            alert('Pilih proyek terlebih dahulu!');
            return;
        }

        if (selectedPeriods.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu periode!');
            return;
        }

        if (!templateType) {
            e.preventDefault();
            alert('Pilih jenis laporan!');
            return;
        }

        if (selectedSections.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu bagian data yang ingin diekspor!');
            return;
        }

        // Show loading
        loadingOverlay.style.display = 'flex';
        
        // Hide loading after 3 seconds (give time for download to start)
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 3000);
    });

    // FUNGSI BARU: Handle Select All / Deselect All Sections
    const btnToggleAllSections = document.getElementById('btnToggleAllSections');
    
    if (btnToggleAllSections) {
        btnToggleAllSections.addEventListener('click', function() {
            const sectionCheckboxes = document.querySelectorAll('input[name="sections[]"]');
            const sectionButtons = document.querySelectorAll('.section-button');
            
            // Cek apakah saat ini semua sudah terpilih
            const isAllChecked = Array.from(sectionCheckboxes).every(cb => cb.checked);
            const newState = !isAllChecked; // Jika semua checked, maka uncheck semua. Jika tidak, check semua.

            // Terapkan status baru ke semua checkbox
            sectionCheckboxes.forEach(cb => {
                cb.checked = newState;
            });

            // Update visual tombol (active class)
            sectionButtons.forEach(btn => {
                if (newState) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Ubah teks tombol
            this.textContent = newState ? 'Hapus Semua' : 'Pilih Semua';
            
            // Ubah style tombol agar terlihat beda saat "Hapus Semua"
            if (newState) {
                this.style.background = '#00AEEF';
                this.style.color = '#fff';
            } else {
                this.style.background = '#fff';
                this.style.color = '#00AEEF';
            }

            // Update Preview document
            updatePreview();
        });
    }

    // Tambahan: Update teks tombol "Pilih Semua" secara manual jika user klik checkbox satu per satu
    document.addEventListener('change', function(e) {
        if (e.target.name === 'sections[]') {
            const sectionCheckboxes = document.querySelectorAll('input[name="sections[]"]');
            const isAllChecked = Array.from(sectionCheckboxes).every(cb => cb.checked);
            
            if (btnToggleAllSections) {
                btnToggleAllSections.textContent = isAllChecked ? 'Hapus Semua' : 'Pilih Semua';
                if (isAllChecked) {
                    btnToggleAllSections.style.background = '#00AEEF';
                    btnToggleAllSections.style.color = '#fff';
                } else {
                    btnToggleAllSections.style.background = '#fff';
                    btnToggleAllSections.style.color = '#00AEEF';
                }
            }
        }
    });

});
</script>
@endsection