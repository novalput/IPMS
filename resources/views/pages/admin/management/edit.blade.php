@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow rounded-4 p-4" style="background:#e5e5e5;">
        <a href="{{ route('user.management') }}" class="btn btn-light mb-3" style="width:120px;">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
        <form action="{{ route('user.management.update', $user) }}" method="POST" style="max-width:700px;">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label text">Username</label>
                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text">Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Biarkan kosong jika tidak ingin mengubah">
                    <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
                        <i class="fa fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text">Role</label>
                <select name="role" class="form-select" required>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User Input</option>
                </select>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-info text-white fw-bold px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>
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
</script>
@endsection