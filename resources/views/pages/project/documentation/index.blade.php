@extends('layouts.app')

@section('title', 'Dokumentasi Proyek - ' . $project->name)

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div style="padding: 1rem;">
    
    {{-- Include CSS --}}
    @include('pages.project.documentation.partials.styles')

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

    <div class="edit-modal" id="editModal">
        <div class="edit-modal-content">
            <div class="edit-modal-title">Edit Foto Dokumentasi</div>
            
            <div class="current-photo-section">
                <span class="current-photo-label">Foto Saat Ini:</span>
                <div class="current-photo-preview">
                    <img id="editCurrentPhoto" src="" alt="Current Photo">
                </div>
            </div>

            <div class="replace-photo-toggle">
                <input type="checkbox" id="replacePhotoToggle" onchange="toggleReplacePhoto()">
                <label for="replacePhotoToggle">
                    <i class="fas fa-exchange-alt"></i>
                    Ganti foto ini dengan foto baru
                </label>
            </div>

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

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <input type="file" id="fileInput" class="file-input" multiple accept="image/*" onchange="handleFileSelect(event)">

    <input type="file" id="editFileInput" class="edit-file-input" accept="image/*" onchange="handleEditFileSelect(event)">

    <div id="toast" class="toast"></div>

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

    {{-- Include Scripts --}}
    @include('pages.project.documentation.partials.scripts')

</div>
@endsection