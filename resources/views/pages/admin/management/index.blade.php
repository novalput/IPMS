@extends('layouts.app')

@section('content')
<style>
/* Modal Styles - FORCE BACKGROUND */
.modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.7) !important; /* Ubah ke background-color */
    z-index: 99999 !important; /* Naikkan z-index */
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
}

.modal-overlay.show {
    display: flex !important;
}

.modal-content {
    position: relative !important;
    background: #ffffff !important;
    border-radius: 12px;
    padding: 0;
    max-width: 440px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3) !important;
    z-index: 100000 !important; /* Lebih tinggi dari overlay */
    animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0; 
        transform: translateY(30px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

.modal-header {
    padding: 24px 24px 0 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    flex-shrink: 0;
}

.modal-icon.danger {
    background: #e74c3c;
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.modal-body {
    padding: 16px 24px 24px 24px;
}

.modal-message {
    font-size: 16px;
    color: #5a6c7d;
    line-height: 1.5;
    margin-bottom: 0;
}

.modal-user-name {
    font-weight: 600;
    color: #2c3e50;
}

.modal-footer {
    padding: 0 24px 24px 24px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.modal-btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    min-width: 100px;
}

.modal-btn-cancel {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

.modal-btn-cancel:hover {
    background: #e9ecef;
    color: #495057;
}

.modal-btn-confirm {
    background: #e74c3c;
    color: #fff;
}

.modal-btn-confirm:hover {
    background: #c82333;
}
</style>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">User Management</h2>
        <a href="{{ route('user.management.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Section untuk Pending Users --}}
    @if($pendingUsers->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-clock me-2"></i>
                User Menunggu Persetujuan ({{ $pendingUsers->count() }})
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingUsers as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $user->username }}</strong>
                                <br><small class="text-muted">{{ $user->name }}</small>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>{{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm" 
                                    onclick="showApproveUserModal('{{ $user->id }}', '{{ addslashes($user->username) }}')">
                                    <i class="fas fa-check me-1"></i>Approve
                                </button>
                                <form id="approveUserForm-{{ $user->id }}" method="POST" action="{{ route('user.management.approve', $user) }}" style="display:none;">
                                    @csrf
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="showRejectUserModal('{{ $user->id }}', '{{ addslashes($user->username) }}')">
                                    <i class="fas fa-times me-1"></i>Reject
                                </button>
                                <form id="rejectUserForm-{{ $user->id }}" method="POST" action="{{ route('user.management.reject', $user) }}" style="display:none;">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Tidak ada user yang menunggu persetujuan saat ini.
    </div>
    @endif

    {{-- Section untuk Approved/Existing Users --}}
    <div class="card shadow rounded-4 p-4" style="background:#f8f9fa;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Daftar User ({{ $users->count() }})
            </h5>
            <div>
                <span class="badge bg-success me-2">Approved: {{ $users->where('status', 'approved')->count() }}</span>
                <span class="badge bg-danger">Rejected: {{ $users->where('status', 'rejected')->count() }}</span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge 
                                @if($user->role == 'admin') bg-danger
                                @elseif($user->role == 'user') bg-primary  
                                @else bg-info @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge 
                                @if($user->status == 'approved') bg-success
                                @elseif($user->status == 'rejected') bg-danger
                                @else bg-warning text-dark @endif">
                                <i class="fas 
                                    @if($user->status == 'approved') fa-check
                                    @elseif($user->status == 'rejected') fa-times
                                    @else fa-clock @endif me-1"></i>
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            @if($user->approvedBy)
                                <div><strong>{{ $user->approvedBy->name }}</strong></div>
                                <small class="text-muted">{{ $user->approved_at->format('d M Y H:i') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('user.management.edit', $user) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <button type="button" class="btn btn-danger btn-sm" 
                                onclick="showDeleteUserModal('{{ $user->id }}', '{{ addslashes($user->username) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="deleteUserForm-{{ $user->id }}" action="{{ route('user.management.destroy', $user) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2"></i>
                            <div>Tidak ada user yang ditemukan</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- Approve Confirmation Modal --}}
<div class="modal-overlay" id="approveUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon" style="background: #28a745;">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="modal-title">Approve User</h3>
        </div>
        <div class="modal-body">
            <p class="modal-message">
                Apakah Anda yakin ingin meng-approve user <span class="modal-user-name" id="approveUserName"></span>?
                <br><br>
                User akan dapat mengakses sistem sesuai dengan role yang ditentukan.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeApproveUserModal()">
                Batal
            </button>
            <button type="button" class="modal-btn" style="background: #28a745; color: #fff;" onclick="confirmApproveUser()">
                Approve
            </button>
        </div>
    </div>
</div>

{{-- Reject Confirmation Modal --}}
<div class="modal-overlay" id="rejectUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon" style="background: #dc3545;">
                <i class="fas fa-times"></i>
            </div>
            <h3 class="modal-title">Reject User</h3>
        </div>
        <div class="modal-body">
            <p class="modal-message">
                Apakah Anda yakin ingin me-reject user <span class="modal-user-name" id="rejectUserName"></span>?
                <br><br>
                User tidak akan dapat mengakses sistem dan data pendaftaran akan tetap tersimpan dengan status "rejected".
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeRejectUserModal()">
                Batal
            </button>
            <button type="button" class="modal-btn" style="background: #dc3545; color: #fff;" onclick="confirmRejectUser()">
                Reject
            </button>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-overlay" id="deleteUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon danger">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="modal-title">Hapus User</h3>
        </div>
        <div class="modal-body">
            <p class="modal-message">
                Apakah Anda yakin ingin menghapus user <span class="modal-user-name" id="deleteUserName"></span>?
                <br><br>
                <strong>Peringatan:</strong> Semua data yang terkait dengan user ini akan ikut terhapus dan tidak dapat dikembalikan.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteUserModal()">
                Batal
            </button>
            <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmDeleteUser()">
                Hapus User
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentApproveUserId = null;
    let currentRejectUserId = null;
    let currentDeleteUserId = null;

    // Approve User Modal Functions
    window.showApproveUserModal = function(userId, username) {
        currentApproveUserId = userId;
        document.getElementById('approveUserName').textContent = username;
        document.getElementById('approveUserModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closeApproveUserModal = function() {
        document.getElementById('approveUserModal').classList.remove('show');
        document.body.style.overflow = '';
        currentApproveUserId = null;
    };

    window.confirmApproveUser = function() {
        if (currentApproveUserId) {
            document.getElementById('approveUserForm-' + currentApproveUserId).submit();
        }
    };

    // Reject User Modal Functions
    window.showRejectUserModal = function(userId, username) {
        currentRejectUserId = userId;
        document.getElementById('rejectUserName').textContent = username;
        document.getElementById('rejectUserModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closeRejectUserModal = function() {
        document.getElementById('rejectUserModal').classList.remove('show');
        document.body.style.overflow = '';
        currentRejectUserId = null;
    };

    window.confirmRejectUser = function() {
        if (currentRejectUserId) {
            document.getElementById('rejectUserForm-' + currentRejectUserId).submit();
        }
    };

    // Delete User Modal Functions
    window.showDeleteUserModal = function(userId, username) {
        currentDeleteUserId = userId;
        document.getElementById('deleteUserName').textContent = username;
        document.getElementById('deleteUserModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closeDeleteUserModal = function() {
        document.getElementById('deleteUserModal').classList.remove('show');
        document.body.style.overflow = '';
        currentDeleteUserId = null;
    };

    window.confirmDeleteUser = function() {
        if (currentDeleteUserId) {
            document.getElementById('deleteUserForm-' + currentDeleteUserId).submit();
        }
    };

    // Close modal when clicking outside
    document.getElementById('approveUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApproveUserModal();
        }
    });

    document.getElementById('rejectUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectUserModal();
        }
    });

    document.getElementById('deleteUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteUserModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeApproveUserModal();
            closeRejectUserModal();
            closeDeleteUserModal();
        }
    });
});
</script>
@endsection