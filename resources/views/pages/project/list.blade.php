@extends('layouts.app')

@section('title', ucfirst($pageType) . ' - Integrated Project Management System')

@section('content')
<div style="padding: 1rem;">
<style>
    /* GLOBAL RESET - HAPUS SEMUA TEXT DECORATION */
    * {
        text-decoration: none !important;
        outline: none !important;
    }
    
    a, a:hover, a:focus, a:active, a:visited {
        text-decoration: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .page-header {
        background: linear-gradient(135deg, #004ADF 0%, #00A2E9 100%);
        color: #fff;
        padding: 32px 24px;
        border-radius: 12px;
        margin-bottom: 32px;
        text-align: center;
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        margin: 0;
    }

    .page-subtitle {
        font-size: 16px;
        margin-top: 8px;
        opacity: 0.9;
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 24px;
        margin-top: 24px;
    }

    .project-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 24px;
        transition: all 0.2s ease;
        border: 1px solid #e0e7ef;
    }

    .project-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .project-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .project-title {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        flex: 1;
        margin-right: 12px;
    }

    .project-status {
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .project-status.approved {
        background: #27ae60;
    }

    .project-status.submission {
        background: #f39c12;
    }

    .project-status.belum_approval {
        background: #e74c3c;
    }

    .project-status.rejected {
        background: #e74c3c;
    }

    .project-description {
        color: #7f8c8d;
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 63px;
    }

    .project-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-top: 16px;
        border-top: 1px solid #ecf0f1;
    }

    .project-periods {
        color: #95a5a6;
        font-size: 13px;
    }

    .project-created {
        color: #95a5a6;
        font-size: 13px;
    }

    .project-actions {
        display: flex;
        justify-content: flex-end;
    }

    .btn-show {
        background: #00AEEF !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none !important;
        transition: background 0.2s;
        display: inline-block;
        outline: none !important;
        box-shadow: none !important;
    }

    .btn-show:hover {
        background: #0080c7 !important;
        color: #fff !important;
        text-decoration: none !important;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 64px;
        color: #bdc3c7;
        margin-bottom: 16px;
    }

    .empty-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .empty-text {
        font-size: 16px;
        line-height: 1.5;
    }

    .btn-create-project {
        background: #004ADF !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none !important;
        display: inline-block;
        margin-top: 24px;
        transition: background 0.2s;
    }

    .btn-create-project:hover {
        background: #002e8f !important;
        color: #fff !important;
        text-decoration: none !important;
    }
</style>

<div class="page-header">
    <h1 class="page-title">{{ ucfirst($pageType) }}</h1>
    <p class="page-subtitle">Pilih proyek untuk melihat {{ $pageType }}</p>
</div>

@if($projects->count() == 0)
    <div class="empty-state">
        <div class="empty-icon">📁</div>
        <h2 class="empty-title">Belum Ada Proyek</h2>
        <p class="empty-text">
            Saat ini belum ada proyek yang tersedia.<br>
            Silakan buat proyek baru untuk memulai.
        </p>
        <a href="{{ route('project.create') }}" class="btn-create-project">
            Tambah Proyek Baru
        </a>
    </div>
@else
    <div class="projects-grid">
        @foreach($projects as $project)
            <div class="project-card">
                <div class="project-header">
                    <h3 class="project-title">{{ $project->name }}</h3>
                    <span class="project-status {{ $project->status }}">
                        @switch($project->status)
                            @case('approved')
                                Approved
                                @break
                            @case('submission')
                                Submission
                                @break
                            @case('belum_approval')
                                Belum Approval
                                @break
                            @case('rejected')
                                Rejected
                                @break
                            @default
                                {{ ucfirst($project->status) }}
                        @endswitch
                    </span>
                </div>

                <div class="project-description">
                    {{ $project->description ?: 'Tidak ada deskripsi.' }}
                </div>

                <div class="project-meta">
                    <div class="project-periods">
                        {{ $project->periods_count ?? $project->periods->count() }} periode
                    </div>
                    <div class="project-created">
                        {{ $project->created_at->format('d M Y') }}
                    </div>
                </div>

                <div class="project-actions">
                    @php
                        $routeName = 'project.' . $pageType;
                        if ($pageType === 'aktivitas') {
                            $routeName = 'project.activities';
                        } elseif ($pageType === 'isu') {
                            $routeName = 'project.issues';
                        } elseif ($pageType === 'dokumentasi') {
                            $routeName = 'project.documentation';
                        }
                    @endphp
                    <a href="{{ route($routeName, $project->id) }}" class="btn-show">
                        Lihat {{ ucfirst($pageType) }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
</div>
@endsection