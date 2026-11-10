@extends('layouts.app')

@section('title', 'Dokumentasi Proyek - ' . $project->name)

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

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

    .documentation-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .project-name {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
    }

    .controls-section {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-upload {
        background: #00AEEF !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-upload:hover {
        background: #0080c7 !important;
        color: #fff !important;
        text-decoration: none !important;
    }

    .period-select {
        background: #00AEEF;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        font-weight: 500;
        min-width: 200px;
        cursor: pointer;
    }

    .period-select option {
        background: #004ADF;
        color: #fff;
    }

    /* Photo Grid Layout */
    .photos-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-top: 24px;
    }

    .photo-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }

    .photo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .photo-container {
        aspect-ratio: 4/3;
        background: #f1f1f1;
        position: relative;
        cursor: pointer;
        overflow: hidden;
    }

    .photo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.2s;
    }

    .photo-placeholder {
        width: 100%;
        height: 100%;
        background: #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 14px;
        text-align: center;
        padding: 16px;
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 14px;
        text-align: center;
        pointer-events: none;
    }

    .photo-container:hover .photo-overlay {
        opacity: 1;
    }

    /* Photo description area dengan actions */
    .photo-description {
        padding: 12px 16px;
        background: #fff;
        border-top: 1px solid #f0f0f0;
        position: relative;
    }

    .photo-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .photo-text {
        flex: 1;
        min-width: 0;
    }

    .photo-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        line-height: 1.3;
        min-height: 18px;
    }

    .photo-desc-text {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
        word-wrap: break-word;
        min-height: 36px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Photo actions di area deskripsi */
    .photo-actions {
        display: flex;
        flex-direction: column;
        gap: 2px;
        opacity: 0;
        transition: opacity 0.2s;
        flex-shrink: 0;
    }

    .photo-card:hover .photo-actions {
        opacity: 1;
    }

    .btn-photo-action {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        color: #6c757d;
        transition: all 0.2s;
    }

    .btn-photo-action:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-photo-action.download {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-photo-action.download:hover {
        background: #28a745;
        color: white;
    }

    .btn-photo-action.edit {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-photo-action.edit:hover {
        background: #ffc107;
        color: #212529;
    }

    .btn-photo-action.delete {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-photo-action.delete:hover {
        background: #dc3545;
        color: white;
    }

    /* Empty state */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #e0e7ef;
    }

    .empty-icon {
        font-size: 48px;
        color: #00AEEF;
        margin-bottom: 16px;
    }

    .empty-text {
        font-size: 18px;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
    }

    .empty-subtext {
        font-size: 14px;
        opacity: 0.7;
        margin-bottom: 20px;
    }

    .empty-upload-btn {
        background: #00AEEF;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .empty-upload-btn:hover {
        background: #0080c7;
    }

    /* Upload Modal */
    .upload-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1001;
        align-items: center;
        justify-content: center;
    }

    .upload-modal.active {
        display: flex;
    }

    .upload-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        min-width: 600px;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .upload-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .upload-area {
        border: 2px dashed #00AEEF;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        background: rgba(0, 174, 239, 0.05);
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 20px;
    }

    .upload-area:hover {
        border-color: #0080c7;
        background: rgba(0, 174, 239, 0.1);
    }

    .upload-area.dragover {
        border-color: #004ADF;
        background: rgba(0, 174, 239, 0.15);
    }

    .upload-icon {
        font-size: 48px;
        color: #00AEEF;
        margin-bottom: 16px;
    }

    .upload-text {
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .upload-subtext {
        font-size: 14px;
        color: #6c757d;
    }

    .selected-files {
        margin-top: 20px;
        max-height: 400px;
        overflow-y: auto;
    }

    .file-item {
        display: flex;
        align-items: flex-start;
        padding: 16px;
        border: 1px solid #e0e7ef;
        border-radius: 8px;
        margin-bottom: 12px;
        background: #f8f9fa;
        position: relative;
    }

    .file-preview {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-name {
        font-weight: 600;
        margin-bottom: 12px;
        font-size: 14px;
        word-break: break-word;
    }

    .file-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }

    .file-remove:hover {
        background: #c0392b;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #2c3e50;
        font-size: 14px;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e0e7ef;
        border-radius: 6px;
        font-size: 14px;
        resize: vertical;
    }

    .form-input:focus {
        outline: none;
        border-color: #00AEEF;
        box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.1);
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e0e7ef;
    }

    .btn-secondary {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-primary {
        background: #00AEEF;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #0080c7;
    }

    .btn-primary:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    /* Enhanced Edit Modal */
    .edit-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1002;
        align-items: center;
        justify-content: center;
    }

    .edit-modal.active {
        display: flex;
    }

    .edit-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        min-width: 500px;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .edit-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    /* Current photo preview in edit modal */
    .current-photo-section {
        margin-bottom: 20px;
    }

    .current-photo-label {
        font-size: 14px;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }

    .current-photo-preview {
        text-align: center;
        margin-bottom: 16px;
    }

    .current-photo-preview img {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e0e7ef;
    }

    /* Replace photo section */
    .replace-photo-section {
        margin-bottom: 20px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .replace-photo-label {
        font-size: 14px;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .replace-upload-area {
        border: 2px dashed #6c757d;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 12px;
    }

    .replace-upload-area:hover {
        border-color: #00AEEF;
        background: rgba(0, 174, 239, 0.05);
    }

    .replace-upload-area.dragover {
        border-color: #00AEEF;
        background: rgba(0, 174, 239, 0.1);
    }

    .replace-upload-area.has-file {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.05);
    }

    .replace-upload-icon {
        font-size: 24px;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .replace-upload-text {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .replace-upload-subtext {
        font-size: 12px;
        color: #9ca3af;
    }

    /* New photo preview */
    .new-photo-preview {
        display: none;
        text-align: center;
        margin-top: 12px;
        padding: 12px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e7ef;
    }

    .new-photo-preview img {
        max-width: 150px;
        max-height: 120px;
        border-radius: 6px;
        object-fit: cover;
    }

    .new-photo-info {
        margin-top: 8px;
        font-size: 12px;
        color: #6c757d;
    }

    .remove-new-photo {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 8px;
    }

    .remove-new-photo:hover {
        background: #c82333;
    }

    /* Checkbox untuk ganti foto */
    .replace-photo-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding: 12px;
        background: #e3f2fd;
        border-radius: 8px;
        border: 1px solid #bbdefb;
    }

    .replace-photo-toggle input[type="checkbox"] {
        width: 16px;
        height: 16px;
    }

    .replace-photo-toggle label {
        font-size: 14px;
        font-weight: 500;
        color: #1976d2;
        cursor: pointer;
        margin: 0;
    }

    /* Hide file input */
    .edit-file-input {
        display: none;
    }

    /* Progress bar untuk edit */
    .edit-progress {
        width: 100%;
        height: 4px;
        background: #e0e7ef;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 12px;
        display: none;
    }

    .edit-progress-bar {
        height: 100%;
        background: #00AEEF;
        width: 0%;
        transition: width 0.3s;
    }

    /* Photo Modal - View Full Size */
    .photo-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .photo-modal.active {
        display: flex;
    }

    .photo-modal-content {
        max-width: 90vw;
        max-height: 90vh;
        position: relative;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    .photo-modal img {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        background: #000;
    }

    .photo-modal-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: rgba(0,0,0,0.7);
        border: none;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-modal-close:hover {
        background: rgba(0,0,0,0.9);
    }

    .photo-modal-info {
        padding: 20px;
        background: #fff;
    }

    .photo-modal-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .photo-modal-description {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .photo-modal-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e0e7ef;
        font-size: 14px;
        color: #6c757d;
    }

    .photo-modal-actions {
        display: flex;
        gap: 8px;
    }

    .photo-modal-btn {
        flex: 1;
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .photo-modal-btn.download {
        background: #28a745;
        color: white;
    }

    .photo-modal-btn.download:hover {
        background: #218838;
    }

    .photo-modal-btn.edit {
        background: #ffc107;
        color: #212529;
    }

    .photo-modal-btn.edit:hover {
        background: #e0a800;
    }

    .photo-modal-btn.delete {
        background: #dc3545;
        color: white;
    }

    .photo-modal-btn.delete:hover {
        background: #c82333;
    }

    .file-input {
        display: none;
    }

    /* Loading state */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1003;
        display: none;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #00AEEF;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Toast notifications */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 1004;
        transform: translateX(100%);
        transition: transform 0.3s;
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast.error {
        background: #dc3545;
    }

    .toast.info {
        background: #17a2b8;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .photos-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 768px) {
        .photos-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .documentation-controls {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .controls-section {
            width: 100%;
            justify-content: space-between;
        }

        .upload-modal-content {
            min-width: 90vw;
            margin: 20px;
        }

        .photo-content {
            flex-direction: column;
            gap: 8px;
        }

        .photo-actions {
            flex-direction: row;
            align-self: flex-end;
        }
    }

    @media (max-width: 480px) {
        .photos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="project-header">
    <div class="project-info">
        <h1>{{ $project->name }}</h1>
        <div class="project-meta">
            <span>Dokumentasi Proyek</span>
            @if($currentPeriod)
                <span>Periode: <strong>{{ $currentPeriod->name }}</strong></span>
            @endif
        </div>
    </div>
</div>

<div class="documentation-controls">
    <div class="project-name">{{ $project->name }}</div>
    <div class="controls-section">
        <button class="btn-upload" onclick="openUploadModal()">
            <i class="fas fa-camera"></i>
            Upload Foto
        </button>
        @if($periods->count() > 1)
            <select class="period-select" onchange="changePeriod(this.value)">
                @foreach($periods as $period)
                    <option value="{{ $period->id }}" {{ $currentPeriod && $currentPeriod->id == $period->id ? 'selected' : '' }}>
                        {{ $period->name }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>
</div>

@if($currentPeriod)
    <div class="photos-grid" id="photosGrid">
        <!-- Photos akan diload di sini via JavaScript -->
    </div>
@else
    <div class="photos-grid">
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="empty-text">Proyek ini belum memiliki periode</div>
            <div class="empty-subtext">Silakan buat periode terlebih dahulu untuk mengelola dokumentasi</div>
        </div>
    </div>
@endif

<!-- Upload Modal -->
<div class="upload-modal" id="uploadModal">
    <div class="upload-modal-content">
        <div class="upload-modal-title">Upload Foto Dokumentasi</div>
        
        <div class="upload-area" onclick="document.getElementById('fileInput').click()">
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="upload-text">Klik untuk pilih foto</div>
            <div class="upload-subtext">atau drag & drop foto di sini (bisa multi pilih)</div>
        </div>
        
        <div class="selected-files" id="selectedFiles" style="display: none;"></div>
        
        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeUploadModal()">Batal</button>
            <button type="button" class="btn-primary" id="uploadButton" onclick="uploadFiles()" disabled>
                <span id="uploadButtonText">Upload</span>
                <span id="uploadButtonCount" style="display: none;"></span>
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Edit Modal -->
<div class="edit-modal" id="editModal">
    <div class="edit-modal-content">
        <div class="edit-modal-title">Edit Foto Dokumentasi</div>
        
        <!-- Current Photo Section -->
        <div class="current-photo-section">
            <span class="current-photo-label">Foto Saat Ini:</span>
            <div class="current-photo-preview">
                <img id="editCurrentPhoto" src="" alt="Current Photo">
            </div>
        </div>

        <!-- Replace Photo Toggle -->
        <div class="replace-photo-toggle">
            <input type="checkbox" id="replacePhotoToggle" onchange="toggleReplacePhoto()">
            <label for="replacePhotoToggle">
                <i class="fas fa-exchange-alt"></i>
                Ganti foto ini dengan foto baru
            </label>
        </div>

        <!-- Replace Photo Section -->
        <div class="replace-photo-section" id="replacePhotoSection" style="display: none;">
            <div class="replace-photo-label">
                <i class="fas fa-upload"></i>
                Pilih Foto Baru:
            </div>
            
            <div class="replace-upload-area" id="editUploadArea" onclick="document.getElementById('editFileInput').click()">
                <div class="replace-upload-icon">
                    <i class="fas fa-image"></i>
                </div>
                <div class="replace-upload-text">Klik untuk pilih foto baru</div>
                <div class="replace-upload-subtext">atau drag & drop foto di sini</div>
            </div>
            
            <div class="new-photo-preview" id="editNewPhotoPreview">
                <img id="editNewPhotoImg" src="" alt="New Photo">
                <div class="new-photo-info">
                    <div id="editNewPhotoName"></div>
                    <div id="editNewPhotoSize"></div>
                </div>
                <button type="button" class="remove-new-photo" onclick="removeNewPhoto()">
                    <i class="fas fa-times"></i> Hapus
                </button>
            </div>
        </div>

        <form id="editForm">
            <div class="form-group">
                <label class="form-label">Judul Foto</label>
                <input type="text" id="editTitle" class="form-input" placeholder="Masukkan judul foto">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea id="editDescription" class="form-input" rows="4" placeholder="Masukkan deskripsi foto"></textarea>
            </div>
            
            <div class="edit-progress" id="editProgress">
                <div class="edit-progress-bar" id="editProgressBar"></div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-primary" id="editSubmitButton">
                    <span id="editSubmitText">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Photo Modal - View Full Size -->
<div class="photo-modal" id="photoModal" onclick="closePhotoModal()">
    <div class="photo-modal-content" onclick="event.stopPropagation()">
        <button class="photo-modal-close" onclick="closePhotoModal()">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalPhoto" src="" alt="Photo">
        <div class="photo-modal-info">
            <div class="photo-modal-title" id="modalTitle"></div>
            <div class="photo-modal-description" id="modalDescription"></div>
            <div class="photo-modal-meta">
                <div>
                    <strong>Ukuran:</strong> <span id="modalFileSize"></span>
                </div>
                <div>
                    <strong>Tanggal:</strong> <span id="modalCreatedAt"></span>
                </div>
            </div>
            <div class="photo-modal-actions">
                <button class="photo-modal-btn download" onclick="downloadPhoto(currentPhotoId)">
                    <i class="fas fa-download"></i>
                    Download
                </button>
                <button class="photo-modal-btn edit" onclick="editPhotoFromModal(currentPhotoId)">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                <button class="photo-modal-btn delete" onclick="deletePhotoFromModal(currentPhotoId)">
                    <i class="fas fa-trash"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- File Input (Hidden) -->
<input type="file" id="fileInput" class="file-input" multiple accept="image/*" onchange="handleFileSelect(event)">

<!-- Hidden File Input for Edit -->
<input type="file" id="editFileInput" class="edit-file-input" accept="image/*" onchange="handleEditFileSelect(event)">

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script>
let selectedFiles = [];
let currentEditId = null;
let currentPhotoId = null;
let editSelectedFile = null;
let isReplacingPhoto = false;
const projectId = {{ $project->id }};
const currentPeriodId = {{ $currentPeriod ? $currentPeriod->id : 'null' }};

function changePeriod(periodId) {
    showLoading();
    window.location.href = "{{ route('project.documentation', $project->id) }}?period_id=" + periodId;
}

// Load existing photos
function loadPhotos() {
    if (!currentPeriodId || currentPeriodId === 'null') {
        console.log('No current period ID');
        return;
    }
    
    console.log('Loading photos for period:', currentPeriodId);
    showLoading();
    
    fetch(`/project/${projectId}/documentation/photos?period_id=${currentPeriodId}`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Photos data:', data);
            if (data.success) {
                if (data.photos.length === 0) {
                    showEmptyState();
                } else {
                    displayPhotos(data.photos);
                }
            } else {
                console.error('Failed to load photos:', data.message);
                showEmptyState();
            }
        })
        .catch(error => {
            console.error('Error loading photos:', error);
            showEmptyState();
        })
        .finally(() => {
            hideLoading();
        });
}

// Display photos in grid
function displayPhotos(photos) {
    const grid = document.getElementById('photosGrid');
    grid.innerHTML = '';
    
    if (photos.length === 0) {
        showEmptyState();
        return;
    }
    
    photos.forEach(photo => {
        const photoCard = createPhotoCard(photo);
        grid.appendChild(photoCard);
    });
}

// Create photo card element dengan actions di area deskripsi
function createPhotoCard(photo) {
    const card = document.createElement('div');
    card.className = 'photo-card';
    card.dataset.photoId = photo.id;
    
    // Create elements separately to avoid escaping issues
    const photoContainer = document.createElement('div');
    photoContainer.className = 'photo-container';
    photoContainer.onclick = () => openPhotoModal(photo.id);
    
    const photoImage = document.createElement('img');
    photoImage.src = photo.file_url;
    photoImage.alt = photo.title;
    photoImage.className = 'photo-image';
    photoImage.onerror = function() {
        this.parentElement.innerHTML = '<div class="photo-placeholder">Foto tidak dapat dimuat</div>';
    };
    
    const photoOverlay = document.createElement('div');
    photoOverlay.className = 'photo-overlay';
    photoOverlay.innerHTML = '<span>Klik untuk melihat full size</span>';
    
    photoContainer.appendChild(photoImage);
    photoContainer.appendChild(photoOverlay);
    
    const photoDescription = document.createElement('div');
    photoDescription.className = 'photo-description';
    photoDescription.innerHTML = `
        <div class="photo-content">
            <div class="photo-text">
                <div class="photo-title">${photo.title || 'Deskripsi Foto'}</div>
                <div class="photo-desc-text">${photo.description || 'Tidak ada deskripsi'}</div>
            </div>
            <div class="photo-actions">
                <button class="btn-photo-action download" onclick="downloadPhoto(${photo.id})" title="Download">
                    <i class="fas fa-download"></i>
                </button>
                <button class="btn-photo-action edit" onclick="editPhoto(${photo.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-photo-action delete" onclick="deletePhoto(${photo.id})" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    card.appendChild(photoContainer);
    card.appendChild(photoDescription);
    
    return card;
}

// Show empty state
function showEmptyState() {
    const grid = document.getElementById('photosGrid');
    grid.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-images"></i>
            </div>
            <div class="empty-text">Belum ada foto dokumentasi</div>
            <div class="empty-subtext">Upload foto pertama untuk memulai dokumentasi proyek periode ini</div>
            <button class="empty-upload-btn" onclick="openUploadModal()">
                <i class="fas fa-camera"></i>
                Upload Foto Sekarang
            </button>
        </div>
    `;
}

// Upload Modal Functions
function openUploadModal() {
    console.log('Opening upload modal, period ID:', currentPeriodId);
    
    if (!currentPeriodId || currentPeriodId === 'null') {
        showToast('Pilih periode terlebih dahulu', 'error');
        return;
    }
    
    const modal = document.getElementById('uploadModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('active');
    document.body.style.overflow = '';
    selectedFiles = [];
    document.getElementById('selectedFiles').style.display = 'none';
    document.getElementById('uploadButton').disabled = true;
    document.getElementById('uploadButtonText').textContent = 'Upload';
    document.getElementById('uploadButtonCount').style.display = 'none';
    document.getElementById('fileInput').value = '';
}

// File handling untuk upload normal
function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    console.log('Files selected:', files.length);
    selectedFiles = files;
    displaySelectedFiles(files);
}

function displaySelectedFiles(files) {
    const container = document.getElementById('selectedFiles');
    container.innerHTML = '';
    
    if (files.length === 0) {
        container.style.display = 'none';
        document.getElementById('uploadButton').disabled = true;
        document.getElementById('uploadButtonText').textContent = 'Upload';
        document.getElementById('uploadButtonCount').style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    document.getElementById('uploadButton').disabled = false;
    document.getElementById('uploadButtonText').textContent = 'Upload';
    document.getElementById('uploadButtonCount').textContent = `(${files.length} foto)`;
    document.getElementById('uploadButtonCount').style.display = 'inline';
    
    files.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.dataset.index = index;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            fileItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="file-preview">
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="form-group">
                        <label class="form-label">Judul Foto</label>
                        <input type="text" class="form-input" placeholder="Masukkan judul foto" 
                               data-index="${index}" data-type="title" value="${file.name.replace(/\.[^/.]+$/, '')}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-input" rows="2" placeholder="Masukkan deskripsi foto" 
                                  data-index="${index}" data-type="description"></textarea>
                    </div>
                </div>
                <button class="file-remove" onclick="removeFile(${index})" title="Hapus file">
                    <i class="fas fa-times"></i>
                </button>
            `;
        };
        reader.readAsDataURL(file);
        
        container.appendChild(fileItem);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    displaySelectedFiles(selectedFiles);
}

// Upload files
function uploadFiles() {
    if (selectedFiles.length === 0) {
        showToast('Pilih foto terlebih dahulu', 'error');
        return;
    }
    
    console.log('Uploading files:', selectedFiles.length);
    showLoading();
    
    const formData = new FormData();
    formData.append('period_id', currentPeriodId);
    
    selectedFiles.forEach((file, index) => {
        formData.append('photos[]', file);
        
        const titleInput = document.querySelector(`input[data-index="${index}"][data-type="title"]`);
        const descInput = document.querySelector(`textarea[data-index="${index}"][data-type="description"]`);
        
        formData.append(`titles[${index}]`, titleInput ? titleInput.value : file.name.replace(/\.[^/.]+$/, ''));
        formData.append(`descriptions[${index}]`, descInput ? descInput.value : '');
    });
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/project/${projectId}/documentation/upload`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken || ''
        },
        body: formData
    })
    .then(response => {
        console.log('Upload response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Upload response:', data);
        if (data.success) {
            showToast(data.message, 'success');
            closeUploadModal();
            loadPhotos();
        } else {
            showToast(data.message || 'Gagal mengupload foto', 'error');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showToast('Terjadi kesalahan saat mengupload', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

// Download Photo
function downloadPhoto(photoId) {
    window.open(`/project/${projectId}/documentation/${photoId}/download`, '_blank');
}

// Photo Modal Functions - View Full Size
function openPhotoModal(photoId) {
    currentPhotoId = photoId;
    showLoading();
    
    fetch(`/project/${projectId}/documentation/${photoId}/photo`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const photo = data.photo;
                const modal = document.getElementById('photoModal');
                const modalPhoto = document.getElementById('modalPhoto');
                const modalTitle = document.getElementById('modalTitle');
                const modalDescription = document.getElementById('modalDescription');
                const modalFileSize = document.getElementById('modalFileSize');
                const modalCreatedAt = document.getElementById('modalCreatedAt');
                
                modalPhoto.src = photo.file_url;
                modalTitle.textContent = photo.title || 'Tanpa Judul';
                modalDescription.textContent = photo.description || 'Tidak ada deskripsi';
                modalFileSize.textContent = photo.file_size || 'Unknown';
                modalCreatedAt.textContent = photo.created_at;
                
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                showToast('Gagal memuat foto', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading photo:', error);
            showToast('Terjadi kesalahan saat memuat foto', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    currentPhotoId = null;
}

function editPhotoFromModal(photoId) {
    closePhotoModal();
    editPhoto(photoId);
}

function deletePhotoFromModal(photoId) {
    closePhotoModal();
    deletePhoto(photoId);
}

// Enhanced Edit Photo Function
function editPhoto(photoId) {
    currentEditId = photoId;
    editSelectedFile = null;
    isReplacingPhoto = false;
    
    // Reset form
    document.getElementById('replacePhotoToggle').checked = false;
    document.getElementById('replacePhotoSection').style.display = 'none';
    document.getElementById('editNewPhotoPreview').style.display = 'none';
    document.getElementById('editUploadArea').classList.remove('has-file');
    document.getElementById('editFileInput').value = '';
    
    showLoading();
    fetch(`/project/${projectId}/documentation/${photoId}/photo`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const photo = data.photo;
                
                // Set current photo
                document.getElementById('editCurrentPhoto').src = photo.file_url;
                document.getElementById('editTitle').value = photo.title || '';
                document.getElementById('editDescription').value = photo.description || '';
                
                document.getElementById('editModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                showToast('Gagal memuat data foto', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading photo:', error);
            showToast('Terjadi kesalahan saat memuat data foto', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

function deletePhoto(photoId) {
    if (!confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
        return;
    }
    
    showLoading();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/project/${projectId}/documentation/${photoId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            loadPhotos(); // Reload the photos grid
        } else {
            showToast(data.message || 'Gagal menghapus foto', 'error');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('Terjadi kesalahan saat menghapus foto', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
    currentEditId = null;
    editSelectedFile = null;
    isReplacingPhoto = false;
}

function toggleReplacePhoto() {
    const toggle = document.getElementById('replacePhotoToggle');
    const section = document.getElementById('replacePhotoSection');
    
    isReplacingPhoto = toggle.checked;
    
    if (isReplacingPhoto) {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
        editSelectedFile = null;
        document.getElementById('editNewPhotoPreview').style.display = 'none';
        document.getElementById('editUploadArea').classList.remove('has-file');
        document.getElementById('editFileInput').value = '';
    }
}

function handleEditFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showToast('File harus berupa gambar', 'error');
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showToast('Ukuran file maksimal 10MB', 'error');
        return;
    }
    
    editSelectedFile = file;
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('editNewPhotoImg').src = e.target.result;
        document.getElementById('editNewPhotoName').textContent = file.name;
        document.getElementById('editNewPhotoSize').textContent = formatFileSize(file.size);
        document.getElementById('editNewPhotoPreview').style.display = 'block';
        document.getElementById('editUploadArea').classList.add('has-file');
    };
    reader.readAsDataURL(file);
}

function removeNewPhoto() {
    editSelectedFile = null;
    document.getElementById('editNewPhotoPreview').style.display = 'none';
    document.getElementById('editUploadArea').classList.remove('has-file');
    document.getElementById('editFileInput').value = '';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Enhanced Edit Form Submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentEditId) return;
    
    const title = document.getElementById('editTitle').value;
    const description = document.getElementById('editDescription').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Show progress
    document.getElementById('editProgress').style.display = 'block';
    document.getElementById('editSubmitButton').disabled = true;
    document.getElementById('editSubmitText').textContent = isReplacingPhoto && editSelectedFile ? 'Mengganti foto...' : 'Menyimpan...';
    
    // Check if replacing photo
    if (isReplacingPhoto && editSelectedFile) {
        // Use FormData for file upload
        const formData = new FormData();
        formData.append('photo', editSelectedFile);
        formData.append('title', title);
        formData.append('description', description);
        
        fetch(`/project/${projectId}/documentation/${currentEditId}/replace`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Foto berhasil diganti!', 'success');
                loadPhotos();
                closeEditModal();
            } else {
                showToast(data.message || 'Gagal mengganti foto', 'error');
            }
        })
        .catch(error => {
            console.error('Replace error:', error);
            showToast('Terjadi kesalahan saat mengganti foto', 'error');
        })
        .finally(() => {
            document.getElementById('editProgress').style.display = 'none';
            document.getElementById('editSubmitButton').disabled = false;
            document.getElementById('editSubmitText').textContent = 'Simpan Perubahan';
        });
    } else {
        // Update only text data
        fetch(`/project/${projectId}/documentation/${currentEditId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({
                title: title,
                description: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadPhotos();
                closeEditModal();
            } else {
                showToast(data.message || 'Gagal mengupdate deskripsi', 'error');
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            showToast('Terjadi kesalahan saat mengupdate', 'error');
        })
        .finally(() => {
            document.getElementById('editProgress').style.display = 'none';
            document.getElementById('editSubmitButton').disabled = false;
            document.getElementById('editSubmitText').textContent = 'Simpan Perubahan';
        });
    }
});

// Drag & Drop for edit upload area
const editUploadArea = document.getElementById('editUploadArea');
if (editUploadArea) {
    editUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    editUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    editUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        if (files.length > 0) {
            // Simulate file input change
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            document.getElementById('editFileInput').files = dt.files;
            handleEditFileSelect({ target: { files: [files[0]] } });
        }
    });
}

// Utility functions
function showLoading() {
    document.getElementById('loadingOverlay').classList.add('active');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.remove('active');
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type}`;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
        closeEditModal();
        closeUploadModal();
    }
});

// Load photos on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded, currentPeriodId:', currentPeriodId);
    loadPhotos();
});
</script>

@endsection