@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow rounded-4 p-4" style="background:#e5e5e5;">
        <a href="{{ route('profile') }}" class="btn password-back-btn mb-3" style="width:120px;">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
        <!-- Notifikasi error -->
        <div id="password-error" class="alert alert-danger d-none" role="alert"></div>
        <form id="passwordForm" action="{{ route('profile.password.update') }}" method="POST" style="max-width:400px;">
            @csrf
            <div class="mb-3">
                <label class="form-label password-label">Password Sebelumnya</label>
                <div class="input-group">
                    <input type="password" name="current_password" class="form-control password-input" id="current_password" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword('current_password', this)" style="cursor:pointer;">
                        <i class="fa fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label password-label">Password baru</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control password-input" id="password" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword('password', this)" style="cursor:pointer;">
                        <i class="fa fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label password-label">Masukkan kembali password baru</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" class="form-control password-input" id="password_confirmation" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword('password_confirmation', this)" style="cursor:pointer;">
                        <i class="fa fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn password-save-btn px-4">Simpan</button>
        </form>
    </div>
</div>

<style>
/* Password Page Styles */
.password-label {
    color: #2c3e50 !important;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.password-input {
    color: #2c3e50 !important;
    background-color: #fff !important;
    border: 1px solid #ddd !important;
    font-size: 14px;
}

.password-input:focus {
    color: #2c3e50 !important;
    background-color: #fff !important;
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.password-toggle {
    color: #6c757d !important;
    background-color: #f8f9fa !important;
    border: 1px solid #ddd !important;
    cursor: pointer;
}

.password-toggle:hover {
    color: #2c3e50 !important;
    background-color: #e9ecef !important;
}

.password-back-btn {
    background-color: #f8f9fa !important;
    border: 1px solid #ddd !important;
    color: #2c3e50 !important;
    text-decoration: none;
    font-weight: 500;
}

.password-back-btn:hover {
    background-color: #e9ecef !important;
    color: #007bff !important;
    text-decoration: none;
}

.password-save-btn {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #fff !important;
    font-weight: 600;
}

.password-save-btn:hover {
    background-color: #138496 !important;
    border-color: #117a8b !important;
    color: #fff !important;
}

/* Alert styles */
.alert-danger {
    color: #721c24 !important;
    background-color: #f8d7da !important;
    border-color: #f5c6cb !important;
}

/* Form general styles */
.form-control {
    color: #2c3e50 !important;
    background-color: #fff !important;
}

.form-control:focus {
    color: #2c3e50 !important;
    background-color: #fff !important;
}

/* Card content color fix */
.card {
    color: #2c3e50 !important;
}

.container {
    color: #2c3e50 !important;
}
</style>
@endsection

@push('scripts')
<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const icon = el.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Validasi password baru dan konfirmasi
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('passwordForm');
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const errorDiv = document.getElementById('password-error');

    form.addEventListener('submit', function(e) {
        if (password.value !== passwordConfirmation.value) {
            e.preventDefault();
            errorDiv.textContent = 'Password baru dan konfirmasi password tidak sama!';
            errorDiv.classList.remove('d-none');
            passwordConfirmation.focus();
        } else {
            errorDiv.classList.add('d-none');
        }
    });
});
</script>
@endpush