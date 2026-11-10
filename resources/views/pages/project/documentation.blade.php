@extends('layouts.app')

@section('title', 'Dokumentasi Proyek - ' . $project->name)

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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

    .delete-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1003;
    align-items: center;
    justify-content: center;
    }

    .delete-modal.active {
        display: flex;
    }

    .delete-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        min-width: 400px;
        max-width: 500px;
        text-align: center;
    }

    .delete-modal-icon {
        font-size: 48px;
        color: #dc3545;
        margin-bottom: 16px;
    }

    .delete-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #2c3e50;
    }

    .delete-modal-message {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .delete-modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-delete-cancel {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
        min-width: 100px;
    }

    .btn-delete-cancel:hover {
        background: #5a6268;
    }

    .btn-delete-confirm {
        background: #dc3545;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
        min-width: 100px;
    }

    .btn-delete-confirm:hover {
        background: #c82333;
    }

    .btn-delete-confirm:disabled {
        background: #ccc;
        cursor: not-allowed;
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

    <div class="header-right">
    <a href="#" class="back-button" id="backButton">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </a>
    </div>
</div>

<div class="documentation-controls">
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
                        @if($period->start_date && $period->end_date)
                            ({{ \Carbon\Carbon::parse($period->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d/m/Y') }})
                        @endif
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

    <!-- modal delete confirmation -->
    <div class="delete-modal" id="deleteModal">
        <div class="delete-modal-content">
            <div class="delete-modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="delete-modal-title">Konfirmasi Hapus Foto</div>
            <div class="delete-modal-message">
                Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="delete-modal-actions">
                <button type="button" class="btn-delete-cancel" onclick="closeDeleteModal()">
                    Batal
                </button>
                <button type="button" class="btn-delete-confirm" id="deleteConfirmButton" onclick="confirmDeletePhoto()">
                    <span id="deleteConfirmText">Hapus Foto</span>
                </button>
            </div>
        </div>
    </div>

<script>
let selectedFiles = [];
let currentEditId = null;
let currentPhotoId = null;
let editSelectedFile = null;
let photoToDelete = null;
let isReplacingPhoto = false;
const projectId = {{ $project->id }};
const currentPeriodId = {{ $currentPeriod ? $currentPeriod->id : 'null' }};

console.log('Project ID:', projectId);
console.log('Current Period ID:', currentPeriodId);

function changePeriod(periodId) {
    showLoading();
    window.location.href = "{{ route('project.documentation', $project->id) }}?period_id=" + periodId;
}

// Load existing photos
function loadPhotos() {
    if (!currentPeriodId) {
        showEmptyState();
        return;
    }

    showLoading();
    
    // Fix URL untuk production - gunakan route web bukan api
    const url = `/project/${projectId}/documentation/photos?period_id=${currentPeriodId}`;

    console.log('Loading photos with URL:', url);
    console.log('Project ID being used:', projectId);
    console.log('Period ID being used:', currentPeriodId);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Photos data received:', data);
            
            if (data.success && data.photos) {
                data.photos.forEach(photo => {
                    console.log('Photo URL:', photo.file_url);
                });
                displayPhotos(data.photos);
            } else {
                console.error('Failed to load photos:', data.message || 'Unknown error');
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
    console.log('Using project ID:', projectId); // Debug log
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
    
    // PERBAIKAN: Pastikan URL menggunakan projectId
    const uploadUrl = `/project/${projectId}/documentation/upload`;
    console.log('Upload URL:', uploadUrl);
    
    fetch(uploadUrl, {
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
    const checkUrl = `/project/${projectId}/documentation/${photoId}/photo-alt`;
    const downloadAltUrl = `/project/${projectId}/documentation/${photoId}/download-alt`;
    const downloadBindUrl = `/project/${projectId}/documentation/${photoId}/download`;

    fetch(checkUrl, {
        method: 'GET',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.ok ? r.json() : Promise.reject(new Error('Check failed')))
    .then(data => {
        if (data.success && data.photo) {
            // Tes ALT dulu (tanpa unduh ke memory)
            return fetch(downloadAltUrl, { method: 'GET' }).then(resp => {
                if (resp.ok) {
                    window.location.href = downloadAltUrl;
                } else {
                    window.location.href = downloadBindUrl;
                }
            }).catch(() => {
                window.location.href = downloadBindUrl;
            });
        } else {
            showToast('File tidak ditemukan', 'error');
        }
    })
    .catch(() => {
        // Jika cek gagal, tetap coba ALT lalu fallback
        fetch(downloadAltUrl, { method: 'GET' }).then(resp => {
            if (resp.ok) {
                window.location.href = downloadAltUrl;
            } else {
                window.location.href = downloadBindUrl;
            }
        }).catch(() => {
            window.location.href = downloadBindUrl;
        });
    });
}

// Photo Modal Functions - View Full Size
// PERBAIKAN: Enhanced Photo Modal Functions dengan error handling yang lebih baik
function openPhotoModal(photoId) {
    currentPhotoId = photoId;
    
    // Tampilkan loading terlebih dahulu
    showLoading();
    
    // Fix: Gunakan endpoint yang benar sesuai route
    // Ganti di function openPhotoModal():
    const url = `/project/${projectId}/documentation/${photoId}/photo-alt`;

    // Debug log untuk melihat URL yang dipanggil
    console.log('Opening photo modal with URL:', url);
    console.log('Project ID being used:', projectId);
    console.log('Photo ID being used:', photoId);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Photo modal response status:', response.status);
        console.log('Photo modal response headers:', response.headers);
        
        // Tangani berbagai status error
        if (response.status === 404) {
            throw new Error('Foto tidak ditemukan');
        } else if (response.status === 500) {
            throw new Error('Terjadi kesalahan server. Silakan coba lagi nanti.');
        } else if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Photo modal data:', data);
        
        if (data.success && data.photo) {
            const modal = document.getElementById('photoModal');
            const img = modal.querySelector('#modalPhoto');
            const title = modal.querySelector('#modalTitle');
            const description = modal.querySelector('#modalDescription');
            const fileSize = modal.querySelector('#modalFileSize');
            const createdAt = modal.querySelector('#modalCreatedAt');
            
            // Set data ke modal
            img.src = data.photo.file_url;
            img.alt = data.photo.title || 'Foto dokumentasi';
            title.textContent = data.photo.title || 'Tanpa Judul';
            description.textContent = data.photo.description || 'Tidak ada deskripsi';
            fileSize.textContent = data.photo.file_size;
            createdAt.textContent = data.photo.created_at;
            
            // Tampilkan peringatan jika file tidak ada
            if (data.photo.file_exists === false) {
                showToast('Peringatan: File foto mungkin tidak tersedia', 'warning');
            }
            
            // Tampilkan modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
        } else {
            throw new Error(data.message || 'Gagal memuat data foto');
        }
    })
    .catch(error => {
        console.error('Error loading photo:', error);
        
        // Tampilkan pesan error yang lebih user-friendly
        let errorMessage = 'Gagal memuat foto';
        if (error.message.includes('404')) {
            errorMessage = 'Foto tidak ditemukan';
        } else if (error.message.includes('500')) {
            errorMessage = 'Terjadi kesalahan server. Silakan refresh halaman dan coba lagi.';
        } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
            errorMessage = 'Koneksi internet bermasalah. Silakan periksa koneksi Anda.';
        } else if (error.message) {
            errorMessage = error.message;
        }
        
        showToast(errorMessage, 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

// TAMBAHAN: Function untuk menampilkan toast dengan type warning
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type}`;
    
    // Tambahkan class untuk warning
    if (type === 'warning') {
        toast.style.background = '#ffc107';
        toast.style.color = '#212529';
    }
    
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
        // Reset style
        if (type === 'warning') {
            toast.style.background = '';
            toast.style.color = '';
        }
    }, 4000); // Tampilkan lebih lama untuk pesan warning
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
    
    // Fix: Gunakan endpoint yang benar
    const url = `/project/${projectId}/documentation/${photoId}/photo-alt`;
    console.log('Edit photo URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Edit photo response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Edit photo data:', data);
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
    photoToDelete = photoId;
    
    // Tampilkan modal konfirmasi
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = '';
    photoToDelete = null;
}

function confirmDeletePhoto() {
    if (!photoToDelete) return;

    // Disable button dan show loading
    const confirmBtn = document.getElementById('deleteConfirmButton');
    const confirmText = document.getElementById('deleteConfirmText');
    
    confirmBtn.disabled = true;
    confirmText.textContent = 'Menghapus...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const deleteUrl = `/project/${projectId}/documentation/${photoToDelete}/delete-alt`;

    const formData = new FormData();
    fetch(deleteUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken || '' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            loadPhotos();
            closeDeleteModal();
        } else {
            showToast(data.message || 'Gagal menghapus foto', 'error');
        }
    })
    .catch(err => {
        console.error('Delete error:', err);
        showToast('Terjadi kesalahan saat menghapus foto', 'error');
    })
    .finally(() => {
        confirmBtn.disabled = false;
        confirmText.textContent = 'Hapus Foto';
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

        fetch(`/project/${projectId}/documentation/${currentEditId}/replace-alt`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: formData
        })
        .then(response => {
            console.log('Replace photo response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
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
        // Update only text data - GANTI ke POST dengan method spoofing
        const updateUrl = `/project/${projectId}/documentation/${currentEditId}/update-alt`;
        console.log('Update URL (POST):', updateUrl);
        
        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
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
        closeDeleteModal();
    }
});

// Load photos on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded, currentPeriodId:', currentPeriodId);
    loadPhotos();
});

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
        // Back to New project
        backButton.href = "{{ route('new_project.index') }}";
        backButton.innerHTML = '<i class="fas fa-arrow-left"></i> Kembali';
    }
});
</script>
</div>
@endsection