// Global Variables
let selectedFiles = [];
let currentEditId = null;
let currentPhotoId = null;
let editSelectedFile = null;
let photoToDelete = null;
let isReplacingPhoto = false;

// Get data from window object (set by Blade template)
const projectId = window.projectId;
const currentPeriodId = window.currentPeriodId;
const csrfToken = window.csrfToken;
const routes = window.routes;
const currentRouteName = window.currentRouteName;

// Debug logging
console.log('=== Documentation Script Loaded ===');
console.log('Project ID:', projectId);
console.log('Current Period ID:', currentPeriodId);
console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
console.log('Routes object:', routes);
console.log('Current Route Name:', currentRouteName);

// Validate critical data
if (!projectId) {
    console.error('ERROR: projectId is not defined!');
}
if (!routes) {
    console.error('ERROR: routes object is not defined!');
}
if (!csrfToken) {
    console.error('ERROR: csrfToken is not defined!');
}

// === PERBAIKAN: Ganti fungsi changePeriod dengan yang lebih robust ===
// Kita tempelkan ke window agar bisa dipanggil oleh onchange di HTML
window.changePeriod = function(periodId) {
    if (!periodId) return;

    // Cara yang lebih aman: Ambil URL saat ini, update parameternya
    const currentUrl = new URL(window.location.href);
    
    // Set parameter period_id
    currentUrl.searchParams.set('period_id', periodId);
    
    // Redirect ke URL baru (reload halaman)
    window.location.href = currentUrl.toString();
}

// === PENTING: Daftarkan fungsi lain yang dipanggil via onclick di HTML ke window ===
// Ini diperlukan karena Vite membungkus JS sebagai module
Object.assign(window, {
    openUploadModal,
    closeUploadModal,
    uploadFiles,
    openEditModal,
    closeEditModal,
    toggleReplacePhoto, 
    handleEditFileSelect,
    closePhotoModal,
    downloadPhoto,
    editPhotoFromModal,
    deletePhotoFromModal,
    openDeleteModal,
    closeDeleteModal,
    confirmDeletePhoto,
    handleFileSelect,
    removeNewPhoto,
    removeFile
});

// Load existing photos
function loadPhotos() {
    if (!currentPeriodId) {
        showEmptyState();
        return;
    }

    // Validate routes object
    if (!routes || !routes.fetchPhotos) {
        console.error('Routes not properly configured');
        console.log('Routes object:', routes);
        showEmptyState();
        return;
    }

    showLoading();
    
    const url = routes.fetchPhotos + `?period_id=${currentPeriodId}`;

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
    const emptyState = document.getElementById('emptyState');
    
    grid.innerHTML = '';
    
    if (photos.length === 0) {
        grid.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }
    
    grid.style.display = 'grid';
    if (emptyState) emptyState.style.display = 'none';
    
    photos.forEach(photo => {
        const photoCard = createPhotoCard(photo);
        grid.appendChild(photoCard);
    });
}

// Create photo card element
function createPhotoCard(photo) {
    const card = document.createElement('div');
    card.className = 'photo-card';
    card.dataset.photoId = photo.id;
    
    const photoContainer = document.createElement('div');
    photoContainer.className = 'photo-container';
    photoContainer.onclick = () => openPhotoModal(photo.id);
    
    const photoImage = document.createElement('img');
    photoImage.src = photo.file_url;
    photoImage.alt = photo.title || 'Documentation Photo';
    photoImage.className = 'photo-image';
    photoImage.onerror = function() {
        this.parentElement.innerHTML = '<div class="photo-placeholder">Foto tidak dapat dimuat</div>';
    };
    
    const photoOverlay = document.createElement('div');
    photoOverlay.className = 'photo-overlay';
    photoOverlay.textContent = 'Klik untuk lihat detail';
    
    photoContainer.appendChild(photoImage);
    photoContainer.appendChild(photoOverlay);
    
    const photoDescription = document.createElement('div');
    photoDescription.className = 'photo-description';
    
    const photoContent = document.createElement('div');
    photoContent.className = 'photo-content';
    
    const photoText = document.createElement('div');
    photoText.className = 'photo-text';
    
    const photoTitle = document.createElement('div');
    photoTitle.className = 'photo-title';
    photoTitle.textContent = photo.title || 'Tanpa Judul';
    
    const photoDescText = document.createElement('div');
    photoDescText.className = 'photo-desc-text';
    photoDescText.textContent = photo.description || 'Tidak ada deskripsi';
    
    photoText.appendChild(photoTitle);
    photoText.appendChild(photoDescText);
    
    const photoActions = document.createElement('div');
    photoActions.className = 'photo-actions';
    
    const downloadBtn = document.createElement('button');
    downloadBtn.className = 'btn-photo-action download';
    downloadBtn.innerHTML = '<i class="fas fa-download"></i>';
    downloadBtn.title = 'Download';
    downloadBtn.onclick = (e) => {
        e.stopPropagation();
        downloadPhoto(photo.id);
    };
    
    const editBtn = document.createElement('button');
    editBtn.className = 'btn-photo-action edit';
    editBtn.innerHTML = '<i class="fas fa-edit"></i>';
    editBtn.title = 'Edit';
    editBtn.onclick = (e) => {
        e.stopPropagation();
        openEditModal(photo.id);
    };
    
    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'btn-photo-action delete';
    deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
    deleteBtn.title = 'Delete';
    deleteBtn.onclick = (e) => {
        e.stopPropagation();
        openDeleteModal(photo.id);
    };
    
    photoActions.appendChild(downloadBtn);
    photoActions.appendChild(editBtn);
    photoActions.appendChild(deleteBtn);
    
    photoContent.appendChild(photoText);
    photoContent.appendChild(photoActions);
    photoDescription.appendChild(photoContent);
    
    card.appendChild(photoContainer);
    card.appendChild(photoDescription);
    
    return card;
}

// Show empty state
function showEmptyState() {
    const grid = document.getElementById('photosGrid');
    const emptyState = document.getElementById('emptyState');
    
    if (grid) grid.style.display = 'none';
    if (emptyState) emptyState.style.display = 'block';
}

// Upload Modal Functions
function openUploadModal() {
    document.getElementById('uploadModal').classList.add('active');
    selectedFiles = [];
    updateSelectedFilesDisplay();
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('active');
    selectedFiles = [];
    document.getElementById('fileInput').value = '';
    updateSelectedFilesDisplay();
}

// File Selection
function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    selectedFiles = [...selectedFiles, ...files];
    updateSelectedFilesDisplay();
}

function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.classList.remove('dragover');
    
    const files = Array.from(event.dataTransfer.files).filter(file => file.type.startsWith('image/'));
    selectedFiles = [...selectedFiles, ...files];
    updateSelectedFilesDisplay();
}

function handleDragOver(event) {
    event.preventDefault();
    event.target.classList.add('dragover');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.target.classList.remove('dragover');
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateSelectedFilesDisplay();
}

function updateSelectedFilesDisplay() {
    const container = document.getElementById('selectedFiles');
    const uploadButton = document.getElementById('uploadButton');
    
    container.innerHTML = '';
    
    if (selectedFiles.length === 0) {
        container.style.display = 'none';
        uploadButton.disabled = true;
        document.getElementById('uploadText').textContent = 'Upload Foto';
        return;
    }
    
    container.style.display = 'block';
    uploadButton.disabled = false;
    document.getElementById('uploadText').textContent = `Upload ${selectedFiles.length} Foto`;
    
    selectedFiles.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'selected-file-item';
        
        const filePreview = document.createElement('div');
        filePreview.className = 'file-preview';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'file-preview-img';
            filePreview.appendChild(img);
        };
        reader.readAsDataURL(file);
        
        const fileInfo = document.createElement('div');
        fileInfo.className = 'file-info';
        
        const fileName = document.createElement('div');
        fileName.className = 'file-name';
        fileName.textContent = file.name;
        
        const fileSize = document.createElement('div');
        fileSize.className = 'file-size';
        fileSize.textContent = formatFileSize(file.size);
        
        fileInfo.appendChild(fileName);
        fileInfo.appendChild(fileSize);
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'file-remove';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeFile(index);
        
        fileItem.appendChild(filePreview);
        fileItem.appendChild(fileInfo);
        fileItem.appendChild(removeBtn);
        
        container.appendChild(fileItem);
    });
}

// Upload Files
function uploadFiles() {
    if (selectedFiles.length === 0) {
        showToast('Pilih foto terlebih dahulu', 'error');
        return;
    }
    
    const formData = new FormData();
    selectedFiles.forEach(file => {
        formData.append('photos[]', file);
    });
    formData.append('period_id', currentPeriodId);
    
    const uploadButton = document.getElementById('uploadButton');
    const progressContainer = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    uploadButton.disabled = true;
    progressContainer.style.display = 'block';
    
    fetch(routes.uploadPhotos, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Foto berhasil diupload', 'success');
            closeUploadModal();
            loadPhotos();
        } else {
            showToast(data.message || 'Gagal upload foto', 'error');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showToast('Terjadi kesalahan saat upload', 'error');
    })
    .finally(() => {
        uploadButton.disabled = false;
        progressContainer.style.display = 'none';
        progressFill.style.width = '0%';
    });
}

// Photo Modal Functions
function openPhotoModal(photoId) {
    currentPhotoId = photoId;
    showLoading();
    
    const url = routes.fetchPhotos + `?period_id=${currentPeriodId}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const photo = data.photos.find(p => p.id === photoId);
                if (photo) {
                    document.getElementById('photoModalImg').src = photo.file_url;
                    document.getElementById('photoModalTitle').textContent = photo.title || 'Tanpa Judul';
                    document.getElementById('photoModalDesc').textContent = photo.description || 'Tidak ada deskripsi';
                    document.getElementById('photoModalDate').textContent = new Date(photo.created_at).toLocaleDateString('id-ID');
                    
                    document.getElementById('photoModal').classList.add('active');
                }
            }
        })
        .catch(error => {
            console.error('Error loading photo:', error);
            showToast('Gagal memuat foto', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.remove('active');
    currentPhotoId = null;
}

function editPhotoFromModal(photoId) {
    closePhotoModal();
    openEditModal(photoId);
}

function deletePhotoFromModal(photoId) {
    closePhotoModal();
    openDeleteModal(photoId);
}

// Download Photo
function downloadPhoto(photoId) {
    showLoading();
    
    const url = routes.fetchPhotos + `?period_id=${currentPeriodId}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const photo = data.photos.find(p => p.id === photoId);
                if (photo) {
                    const link = document.createElement('a');
                    link.href = photo.file_url;
                    link.download = photo.title || 'photo.jpg';
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    showToast('Download dimulai', 'success');
                }
            }
        })
        .catch(error => {
            console.error('Download error:', error);
            showToast('Gagal download foto', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

// Edit Modal Functions
function openEditModal(photoId) {
    currentEditId = photoId;
    isReplacingPhoto = false;
    editSelectedFile = null;
    
    document.getElementById('replacePhotoCheckbox').checked = false;
    document.getElementById('newPhotoSection').style.display = 'none';
    document.getElementById('editNewPhotoPreview').style.display = 'none';
    
    showLoading();
    
    const url = routes.fetchPhotos + `?period_id=${currentPeriodId}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const photo = data.photos.find(p => p.id === photoId);
                if (photo) {
                    document.getElementById('editCurrentPhoto').src = photo.file_url;
                    document.getElementById('editTitle').value = photo.title || '';
                    document.getElementById('editDescription').value = photo.description || '';
                    
                    document.getElementById('editModal').classList.add('active');
                }
            }
        })
        .catch(error => {
            console.error('Error loading photo:', error);
            showToast('Gagal memuat data foto', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    currentEditId = null;
    isReplacingPhoto = false;
    editSelectedFile = null;
    document.getElementById('editFileInput').value = '';
}

function toggleReplacePhoto() {
    isReplacingPhoto = document.getElementById('replacePhotoCheckbox').checked;
    const newPhotoSection = document.getElementById('newPhotoSection');
    
    if (isReplacingPhoto) {
        newPhotoSection.style.display = 'block';
    } else {
        newPhotoSection.style.display = 'none';
        editSelectedFile = null;
        document.getElementById('editNewPhotoPreview').style.display = 'none';
        document.getElementById('editFileInput').value = '';
    }
}

function handleEditFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.startsWith('image/')) {
        showToast('File harus berupa gambar', 'error');
        return;
    }
    
    if (file.size > 10 * 1024 * 1024) {
        showToast('Ukuran file maksimal 10MB', 'error');
        return;
    }
    
    editSelectedFile = file;
    
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

// Edit Form Submission
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentEditId) return;
            
            const title = document.getElementById('editTitle').value;
            const description = document.getElementById('editDescription').value;
            
            document.getElementById('editProgress').style.display = 'block';
            document.getElementById('editSubmitButton').disabled = true;
            document.getElementById('editSubmitText').textContent = isReplacingPhoto && editSelectedFile ? 'Mengganti foto...' : 'Menyimpan...';
            
            if (isReplacingPhoto && editSelectedFile) {
                const formData = new FormData();
                formData.append('photo', editSelectedFile);
                formData.append('title', title);
                formData.append('description', description);

                fetch(`${routes.replacePhotoBase}${currentEditId}/replace-alt`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
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
                const updateUrl = `${routes.updatePhotoBase}${currentEditId}/update-alt`;
                console.log('Update URL (POST):', updateUrl);
                
                fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
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
    }

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
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                document.getElementById('editFileInput').files = dt.files;
                handleEditFileSelect({ target: { files: [files[0]] } });
            }
        });
    }
});

// Delete Modal Functions
function openDeleteModal(photoId) {
    photoToDelete = photoId;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    photoToDelete = null;
}

function confirmDelete() {
    if (!photoToDelete) return;
    
    const deleteButton = document.getElementById('deleteConfirmButton');
    const deleteText = document.getElementById('deleteConfirmText');
    
    deleteButton.disabled = true;
    deleteText.textContent = 'Menghapus...';
    
    fetch(`${routes.deletePhotoBase}${photoToDelete}/delete-alt`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Foto berhasil dihapus', 'success');
            loadPhotos();
            closeDeleteModal();
        } else {
            showToast(data.message || 'Gagal menghapus foto', 'error');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('Terjadi kesalahan saat menghapus foto', 'error');
    })
    .finally(() => {
        deleteButton.disabled = false;
        deleteText.textContent = 'Hapus Foto';
    });
}

// Alias for delete function
function confirmDeletePhoto() {
    confirmDelete();
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
    
    // Smart back button functionality
    const backButton = document.getElementById('backButton');
    if (backButton && routes) {
        const urlParams = new URLSearchParams(window.location.search);
        const referrer = document.referrer;
        
        if (urlParams.has('from_search') || 
            (referrer && referrer.includes('/search')) || 
            (urlParams.has('search') || urlParams.has('filter') || urlParams.has('start_date') || urlParams.has('end_date'))) {
            
            if (routes.search) {
                let searchUrl = routes.search;
                let searchParams = new URLSearchParams();
                
                if (urlParams.has('search')) searchParams.set('search', urlParams.get('search'));
                if (urlParams.has('filter')) searchParams.set('filter', urlParams.get('filter'));
                if (urlParams.has('start_date')) searchParams.set('start_date', urlParams.get('start_date'));
                if (urlParams.has('end_date')) searchParams.set('end_date', urlParams.get('end_date'));
                
                if (searchParams.toString()) {
                    searchUrl += '?' + searchParams.toString();
                }
                
                backButton.href = searchUrl;
                backButton.innerHTML = '<i class="fas fa-arrow-left"></i> Kembali ke Hasil Pencarian';
            }
        } else {
            if (routes.newProject) {
                backButton.href = routes.newProject;
                backButton.innerHTML = '<i class="fas fa-arrow-left"></i> Kembali';
            }
        }
    } else if (backButton && !routes) {
        console.error('Routes object not available for back button');
    }
});