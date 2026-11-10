@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow rounded-4 p-4" style="background:#e5e5e5;">
        <div class="d-flex flex-column align-items-center">
            <!-- Avatar -->
            <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="position-relative">
                    <img src="{{ str_starts_with(Auth::user()->avatar_url ?? '', 'images/avatar/') ? asset('storage/' . Auth::user()->avatar_url) : asset(Auth::user()->avatar_url ?? 'images/avatar-default.svg') }}" class="rounded-circle" width="160" height="160" alt="Avatar">
                    <label for="avatar" class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow" style="cursor:pointer;">
                        <i class="fa fa-pencil-alt text-dark"></i>
                        <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                    </label>
                </div>
            </form>
            <!-- Modal Crop -->
            <div class="modal fade" id="cropModal" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-body">
                    <img id="cropper-image" style="max-width:100%;">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="cropBtn">Crop & Upload</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Profile Form -->
            <form action="{{ route('profile.update') }}" method="POST" class="w-100 mt-3" style="max-width:700px;">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label profile-label">Nama Lengkap</label>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control profile-input" value="{{ Auth::user()->name }}" required>
                            <span class="input-group-text profile-icon"><i class="bi bi-pencil"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label profile-label">Username</label>
                        <div class="input-group">
                            <input type="text" name="username" class="form-control profile-input" value="{{ Auth::user()->username }}" required>
                            <span class="input-group-text profile-icon"><i class="bi bi-pencil"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label profile-label">Posisi/Jabatan</label>
                        <div class="input-group">
                            <input type="text" name="job_position" class="form-control profile-input" value="{{ Auth::user()->job_position }}" placeholder="Contoh: Project Manager">
                            <span class="input-group-text profile-icon"><i class="bi bi-pencil"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label profile-label">Role</label>
                        <input type="text" class="form-control profile-input-disabled" value="{{ Auth::user()->role }}" disabled>
                        <small class="profile-note">Hubungi Admin jika ingin mengganti role</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label profile-label">Alamat Email</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control profile-input" value="{{ Auth::user()->email }}" required>
                            <span class="input-group-text profile-icon"><i class="bi bi-pencil"></i></span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('profile.password') }}" class="btn profile-btn-password w-100">
                        <span class="profile-btn-text">Ganti Password</span>
                        <span class="float-end"><i class="bi bi-chevron-right"></i></span>
                    </a>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Profile Page Styles */
.profile-label {
    color: #2c3e50 !important;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.profile-input {
    color: #2c3e50 !important;
    background-color: #fff !important;
    border: 1px solid #ddd !important;
    font-size: 14px;
}

.profile-input:focus {
    color: #2c3e50 !important;
    background-color: #fff !important;
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.profile-input-disabled {
    color: #6c757d !important;
    background-color: #f8f9fa !important;
    border: 1px solid #ddd !important;
    font-size: 14px;
}

.profile-input::placeholder {
    color: #6c757d !important;
}

.profile-icon {
    color: #6c757d !important;
    background-color: #f8f9fa !important;
    border: 1px solid #ddd !important;
}

.profile-note {
    color: #6c757d !important;
    font-size: 12px;
    margin-top: 4px;
}

.profile-btn-password {
    background-color: #fff !important;
    border: 1px solid #ddd !important;
    color: #2c3e50 !important;
    font-weight: 600;
    text-align: left;
    padding: 12px 16px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.profile-btn-password:hover {
    background-color: #f8f9fa !important;
    color: #007bff !important;
    text-decoration: none;
}

.profile-btn-text {
    color: #2c3e50 !important;
}

.profile-btn-password:hover .profile-btn-text {
    color: #007bff !important;
}

/* Modal styles */
.modal-content {
    color: #2c3e50 !important;
}

.modal-body {
    color: #2c3e50 !important;
}

/* Button styles */
.btn-primary {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
}

.btn-secondary {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}
</style>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    let cropper;
    const avatarInput = document.getElementById('avatar');
    const cropperImage = document.getElementById('cropper-image');
    const cropModalEl = document.getElementById('cropModal');
    const cropModal = new bootstrap.Modal(cropModalEl);
    const cropBtn = document.getElementById('cropBtn');

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                cropperImage.src = event.target.result;
                cropModal.show();
                if (cropper) cropper.destroy();
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                });
            };
            reader.readAsDataURL(file);
        }
    });

    cropBtn.addEventListener('click', function() {
        if (!cropper) return;
        cropper.getCroppedCanvas().toBlob(function(blob) {
            const formData = new FormData();
            formData.append('avatar', blob, 'avatar.jpg');
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('profile.avatar.update') }}", {
                method: "POST",
                body: formData,
            }).then(() => {
                window.location.reload();
            });
        }, 'image/jpeg');
        cropModal.hide();
    });
});
</script>