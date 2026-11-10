@extends('layouts.app')

@section('title', 'Pencarian - Integrated Project Management System')

@section('content')
<style>
    .search-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .search-header {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .search-title {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }

    .search-controls {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px 12px 45px;
        border: 2px solid #e0e6ed;
        border-radius: 25px;
        font-size: 16px;
        background: #fff;
        color: #333;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .search-input:focus {
        outline: none;
        border-color: #00AEEF;
        box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        font-size: 18px;
        pointer-events: none;
    }

    .date-range {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-input {
        padding: 10px 12px;
        border: 2px solid #e0e6ed;
        border-radius: 8px;
        font-size: 14px;
        width: 140px;
    }

    .date-input:focus {
        outline: none;
        border-color: #00AEEF;
    }

    .btn-search {
        background: #00AEEF;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-search:hover {
        background: #0080c7;
    }

    .filter-tabs {
        display: flex;
        gap: 4px;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 20px;
    }

    .filter-tab {
        padding: 12px 20px;
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
        text-decoration: none;
    }

    .filter-tab:hover {
        background: #f8f9fa;
        color: #333;
        text-decoration: none;
    }

    .filter-tab.active {
        background: #00AEEF;
        color: #fff;
    }

    .search-results {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eee;
    }

    .results-count {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .results-info {
        color: #666;
        font-size: 14px;
    }

    .result-item {
        padding: 20px;
        border: 1px solid #e0e6ed;
        border-radius: 10px;
        margin-bottom: 16px;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .result-item:hover {
        border-color: #00AEEF;
        box-shadow: 0 4px 12px rgba(0, 174, 239, 0.15);
        text-decoration: none;
        color: inherit;
        transform: translateY(-2px);
    }

    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .result-type {
        background: #00AEEF;
        color: #fff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    /* Tambahan warna khusus untuk Documentation */
    .result-item:has(.result-type:contains("Documentation")) .result-type {
        background: #9b59b6;
    }

    .result-status {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .result-status.ongoing {
        background: #fff3cd;
        color: #856404;
    }

    .result-status.done {
        background: #d4edda;
        color: #155724;
    }

    .result-status.delayed {
        background: #f8d7da;
        color: #721c24;
    }

    .result-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .result-meta {
        display: flex;
        gap: 24px;
        color: #666;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .result-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .result-meta-item i {
        color: #00AEEF;
        width: 16px;
    }

    .result-description {
        color: #555;
        font-size: 14px;
        line-height: 1.5;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #00AEEF, #004ADF);
        transition: width 0.3s ease;
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .no-results i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 16px;
    }

    .no-results h3 {
        font-size: 20px;
        margin-bottom: 8px;
        color: #333;
    }

    .btn-back {
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-back:hover {
        background: #5a6268;
        text-decoration: none;
        color: #fff;
    }
</style>

<div class="search-container">
    <!-- Search Header -->
    <div class="search-header">
        <div class="search-title">
            <i class="fa fa-search"></i> Pencarian Proyek
        </div>
        
        <!-- Search Form -->
        <form method="GET" action="{{ route('search') }}" id="searchForm">
            @csrf
            <div class="search-controls">
                <div class="search-box">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" class="search-input" name="search" placeholder="Kata Kunci" value="{{ $searchTerm }}">
                </div>
                
                <div class="date-range">
                    <input type="date" class="date-input" name="start_date" value="{{ $startDate }}">
                    <span>-</span>
                    <input type="date" class="date-input" name="end_date" value="{{ $endDate }}">
                </div>
                
                <button type="submit" class="btn-search">
                    <i class="fa fa-search"></i> Cari
                </button>
                
                <a href="{{ route('dashboard') }}" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button type="button" 
                        class="filter-tab {{ $filter === 'semua' ? 'active' : '' }}" 
                        data-filter="semua">
                    Semua
                </button>
                <button type="button" 
                        class="filter-tab {{ $filter === 'progress' ? 'active' : '' }}" 
                        data-filter="progress">
                    Progress
                </button>
                <button type="button" 
                        class="filter-tab {{ $filter === 'aktivitas' ? 'active' : '' }}" 
                        data-filter="aktivitas">
                    Aktivitas
                </button>
                <button type="button" 
                        class="filter-tab {{ $filter === 'isu' ? 'active' : '' }}" 
                        data-filter="isu">
                    Isu
                </button>
                <button type="button" 
                        class="filter-tab {{ $filter === 'dokumentasi' ? 'active' : '' }}" 
                        data-filter="dokumentasi">
                    Dokumentasi
                </button>
            </div>
            
            <!-- Hidden filter input -->
            <input type="hidden" name="filter" value="{{ $filter }}" id="filterInput">
        </form>
    </div>

    <!-- Search Results -->
    <div class="search-results">
        <div class="results-header">
            <div class="results-count">
                {{ $results->count() }} hasil ditemukan
            </div>
            <div class="results-info">
                @if($searchTerm || $startDate || $endDate)
                    Filter aktif: 
                    @if($searchTerm) "{{ $searchTerm }}" @endif
                    @if($startDate || $endDate)
                        @if($searchTerm) | @endif
                        {{ $startDate ?: 'Awal' }} - {{ $endDate ?: 'Akhir' }}
                    @endif
                    | {{ ucfirst($filter) }}
                @endif
            </div>
        </div>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        @if($results->count() > 0)
            @foreach($results as $result)
                <a href="{{ $result['url'] }}" class="result-item">
                    <div class="result-header">
                        <span class="result-type">{{ $result['type'] }}</span>
                        @if(isset($result['status']))
                            <span class="result-status {{ $result['status'] }}">{{ $result['status'] }}</span>
                        @endif
                    </div>
                    
                    <div class="result-title">{{ $result['title'] }}</div>
                    
                    <div class="result-meta">
                        <div class="result-meta-item">
                            <i class="fa fa-project-diagram"></i>
                            <span>{{ $result['project_name'] }}</span>
                        </div>
                        <div class="result-meta-item">
                            <i class="fa fa-calendar"></i>
                            <span>{{ $result['period_name'] }}</span>
                        </div>
                        @if(isset($result['progress']))
                            <div class="result-meta-item">
                                <i class="fa fa-chart-line"></i>
                                <span>{{ number_format($result['progress'], 1) }}%</span>
                            </div>
                        @endif
                        @if(isset($result['location']))
                            <div class="result-meta-item">
                                <i class="fa fa-map-marker-alt"></i>
                                <span>{{ $result['location'] }}</span>
                            </div>
                        @endif
                        @if(isset($result['approval_status']))
                            <div class="result-meta-item">
                                <i class="fa fa-check-circle"></i>
                                <span>Status: {{ $result['approval_status'] }}</span>
                            </div>
                        @endif
                        @if(isset($result['file_path']) && $result['file_path'])
                            <div class="result-meta-item">
                                <i class="fa fa-file"></i>
                                <span>File tersedia</span>
                            </div>
                        @endif
                    </div>
                    
                    @if(isset($result['description']) && $result['description'])
                        <div class="result-description">{{ Str::limit($result['description'], 150) }}</div>
                    @endif
                    
                    @if(isset($result['progress']) && $result['progress'] > 0)
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $result['progress'] }}%"></div>
                        </div>
                    @endif
                </a>
            @endforeach
        @else
            <div class="no-results">
                <i class="fa fa-search"></i>
                <h3>Tidak ada hasil ditemukan</h3>
                <p>Coba ubah kata kunci atau filter pencarian Anda.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const filterInput = document.getElementById('filterInput');
    const filterTabs = document.querySelectorAll('.filter-tab');

    // Handle filter tab clicks with proper form submission
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Update filter value
            const filterValue = this.getAttribute('data-filter');
            filterInput.value = filterValue;
            
            // Update active state
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Submit form with proper method
            searchForm.submit();
        });
    });

    // Handle search input - only submit on Enter key
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });
    }

    // Prevent form submission issues
    searchForm.addEventListener('submit', function(e) {
        // Ensure we're using GET method
        this.method = 'GET';
        
        // Remove empty inputs to clean URL
        const inputs = this.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.type !== 'hidden' && !input.value.trim()) {
                input.removeAttribute('name');
            }
        });
    });

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(e) {
        location.reload();
    });
});
</script>
@endsection