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
    
    // Fungsi ini akan ditimpa oleh definisi di paling bawah yang lebih kompleks, 
    // tapi tetap didefinisikan untuk keamanan.
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
        
        const photoContainer = document.createElement('div');
        photoContainer.className = 'photo-container';
        photoContainer.onclick = () => openPhotoModal(photo.id);
        
        const photoImage = document.createElement('img');
        // Bagian KUNCI: Gunakan file_url langsung dari controller
        photoImage.src = photo.file_url; 
        photoImage.alt = photo.title;
        photoImage.className = 'photo-image';
        
        // Fallback image jika error (PENTING)
        photoImage.onerror = function() {
            // Ganti src ke placeholder atau tampilkan teks error
            this.onerror = null; // Mencegah loop
            this.parentElement.innerHTML = `
                <div class="photo-placeholder" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; background:#f8f9fa; color:#6c757d;">
                    <i class="fas fa-exclamation-circle" style="font-size:24px; margin-bottom:8px;"></i>
                    <span>Gambar Hilang</span>
                </div>
            `;
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
    
    // Override changePeriod untuk mempertahankan filter search
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