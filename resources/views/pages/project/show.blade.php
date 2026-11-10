@extends('layouts.app')

@section('title', 'Detail Proyek - ' . $project->name)

@section('content')
<style>
    .project-header {
        background: #00A2E9;
        color: #fff;
        padding: 20px 24px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    .project-nav {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 12px;
    }
    .nav-item {
        padding: 12px 24px;
        background: #f8f9fa;
        color: #333;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.15s;
    }
    .nav-item:hover, .nav-item.active {
        background: #00A2E9;
        color: #fff;
        text-decoration: none;
    }
    .project-info {
        background: #fff;
        border: 2px solid #004ADF;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .period-selector {
        margin-bottom: 20px;
    }
    .period-selector select {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
</style>

<div class="project-header">
    <h1>{{ $project->name }}</h1>
    <p style="margin: 8px 0 0 0; opacity: 0.9;">Status: {{ ucfirst($project->status ?? 'draft') }}</p>
</div>

<div class="project-nav">
    <a href="{{ route('project.description', $project->id) }}" class="nav-item active">Deskripsi</a>
    <a href="{{ route('project.progress', $project->id) }}" class="nav-item">Progress</a>
    <a href="{{ route('project.activities', $project->id) }}" class="nav-item">Aktivitas</a>
    <a href="{{ route('project.issues', $project->id) }}" class="nav-item">Isu</a>
    <a href="{{ route('project.documentation', $project->id) }}" class="nav-item">Dokumentasi</a>
</div>

<div class="project-info">
    <h3>Informasi Proyek</h3>
    <div style="margin-top: 16px;">
        <p><strong>Nama Proyek:</strong> {{ $project->name }}</p>
        <p><strong>Deskripsi:</strong> {{ $project->description ?: 'Tidak ada deskripsi' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($project->status ?? 'draft') }}</p>
        <p><strong>Jumlah Periode:</strong> {{ $project->periods->count() }}</p>
        @if($currentPeriod)
            <p><strong>Periode Aktif:</strong> {{ $currentPeriod->name }}</p>
            <p><strong>Tanggal:</strong> {{ $currentPeriod->start_date }} s/d {{ $currentPeriod->end_date }}</p>
        @endif
    </div>
</div>

@if($project->periods->count() > 0)
<div class="project-info">
    <h3>Ringkasan Data</h3>
    <div style="margin-top: 16px;">
        @if($currentPeriod)
            <p><strong>EPC:</strong> {{ $currentPeriod->epc->count() }} item</p>
            <p><strong>Engineering:</strong> {{ $currentPeriod->engineerings->count() }} dokumen</p>
            <p><strong>Procurement Material:</strong> {{ $currentPeriod->procurementMaterials->count() }} item</p>
            <p><strong>Procurement Service:</strong> {{ $currentPeriod->procurementServices->count() }} item</p>
            <p><strong>Construction:</strong> {{ $currentPeriod->constructions->count() }} item</p>
            <p><strong>Activities:</strong> {{ $currentPeriod->activities->count() }} aktivitas</p>
            <p><strong>Issues:</strong> {{ $currentPeriod->issues->count() }} isu</p>
        @else
            <p>Tidak ada periode tersedia.</p>
        @endif
    </div>
</div>
@endif

<div style="margin-top: 24px;">
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    <a href="{{ route('project.edit', $project->id) }}" class="btn btn-primary" style="margin-left: 12px;">Edit Proyek</a>
</div>

@endsection