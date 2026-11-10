@extends('layouts.app')

@section('title', 'Review Proyek - Integrated Project Management System')

@section('content')
<style>
    .reviewer-container {
        max-width: 800px;
        margin: 20px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 24px;
    }
    .section-header {
        background: #009FDF;
        color: #fff;
        padding: 10px 15px;
        border-radius: 6px;
        margin: 20px 0 15px 0;
        font-weight: 500;
    }
    .project-info {
        margin-bottom: 20px;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 6px;
    }
    .info-row {
        margin-bottom: 10px;
        display: flex;
    }
    .info-label {
        font-weight: 500;
        width: 150px;
    }
    .btn-action {
        background: #009FDF;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 10px 24px;
        font-size: 16px;
        transition: background 0.2s;
    }
    .btn-action:hover {
        background: #007bb5;
        color: #fff;
    }
    .btn-secondary-action {
        background: #e0e0e0;
        color: #333;
    }
    .btn-secondary-action:hover {
        background: #c0c0c0;
        color: #333;
    }
    .btn-approve {
        background: #28a745;
    }
    .btn-approve:hover {
        background: #218838;
    }
    .btn-reject {
        background: #dc3545;
    }
    .btn-reject:hover {
        background: #c82333;
    }
    .actions-row {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }
    .table-responsive {
        margin-bottom: 20px;
    }
    .comments-section {
        margin: 30px 0;
    }
    .comments-section textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        min-height: 100px;
        margin-top: 10px;
    }
</style>

<div class="reviewer-container">
    <h2>Review Proyek</h2>
    
    <div class="project-info">
        <div class="info-row">
            <span class="info-label">Nama Proyek:</span>
            <span>{{ $project->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span>{{ $period->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Disubmit oleh:</span>
            <span>{{ $project->creator->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Submit:</span>
            <span>{{ $period->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="section-header">Deskripsi Proyek</div>
    <div class="description-content">
        {{ $project->description }}
    </div>

    <div class="section-header">EPC</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <!-- EPC table content similar to input form but in read-only mode -->
        </table>
    </div>
    
    <div class="section-header">Engineering</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <!-- Engineering table content similar to input form but in read-only mode -->
        </table>
    </div>
    
    <div class="section-header">Procurement</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <!-- Procurement table content similar to input form but in read-only mode -->
        </table>
    </div>
    
    <div class="section-header">Construction</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <!-- Construction table content similar to input form but in read-only mode -->
        </table>
    </div>
    
    <div class="section-header">Aktivitas Proyek</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <!-- Activities table content similar to input form but in read-only mode -->
        </table>
    </div>
    
    <div class="comments-section">
        <h4>Komentar & Catatan Review</h4>
        <form action="{{ route('project.review.submit', $period->id) }}" method="POST">
            @csrf
            <textarea name="review_comments" placeholder="Masukkan komentar atau catatan review di sini..."></textarea>
            
            <div class="actions-row">
                <a href="{{ route('dashboard') }}" class="btn btn-action btn-secondary-action">Kembali</a>
                <div>
                    <button type="submit" name="action" value="reject" class="btn btn-action btn-reject">Tolak</button>
                    <button type="submit" name="action" value="approve" class="btn btn-action btn-approve">Setujui</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection