@extends('layouts.app')

@section('title', 'Progress Proyek - ' . $project->name)

@section('content')
<div style="padding: 1rem;">
<style>
    .project-header {
        background: linear-gradient(135deg, #004ADF 0%, #00A2E9 100%);
        color: #fff;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .project-info h1 {
        font-size: 28px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }

    .project-meta {
        display: flex;
        gap: 24px;
        font-size: 14px;
        opacity: 0.9;
    }

    .period-selector {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 12px 16px;
        min-width: 200px;
    }

    .period-dropdown {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 16px;
        font-weight: 500;
        width: 100%;
        outline: none;
    }

    .period-dropdown option {
        background: #004ADF;
        color: #fff;
    }

    .section-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .section-header {
        background: #00AEEF;
        color: #fff;
        padding: 16px 24px;
        font-size: 18px;
        font-weight: 600;
    }

    .section-content {
        padding: 24px;
    }

    .progress-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .progress-table th,
    .progress-table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #e0e7ef;
        font-size: 14px;
    }

    .progress-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
    }

    .progress-table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-ongoing {
        background: #fff3cd;
        color: #856404;
    }

    .status-done {
        background: #d4edda;
        color: #155724;
    }

    .status-delayed {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    .formula-header {
        font-size: 12px;
        color: #6c757d;
    }

    /* Back Button Styles */
    .back-button {
        background: rgba(255,255,255,0.95) !important;
        color: #004ADF !important;
        border: 2px solid rgba(255,255,255,1) !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        margin-right: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .back-button:hover {
        background: rgba(255,255,255,1) !important;
        color: #002e8f !important;
        border-color: rgba(255,255,255,1) !important;
        text-decoration: none !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .back-button i {
        font-size: 16px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }
</style>

<div class="project-header">
    <div class="project-info">
        <h1>{{ $project->name }}</h1>
        <div class="project-meta">
            <span>Progress Proyek</span>
            @if($currentPeriod)
                <span>Periode: <strong>{{ $currentPeriod->name }}</strong></span>
            @endif
        </div>
    </div>

    <div class="header-right">
        <a href="#" class="back-button" id="backButton">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
        
        @if($periods->count() > 1)
            <div class="period-selector">
                <select class="period-dropdown" onchange="changePeriod(this.value)">
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ $currentPeriod && $currentPeriod->id == $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                            @if($period->start_date && $period->end_date)
                                ({{ \Carbon\Carbon::parse($period->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d/m/Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>

@if($currentPeriod)
    <!-- EPC Progress -->
    <div class="section-card">
        <div class="section-header">EPC Progress</div>
        <div class="section-content">
            @if($currentPeriod->epc && $currentPeriod->epc->count() > 0)
                <table class="progress-table">
                    <thead>
                        <tr>
                            <th rowspan="3">No</th>
                            <th rowspan="3">Description</th>
                            <th rowspan="2">Weight Factor</th>
                            <th colspan="3">Cumulative Last</th>
                            <th colspan="2">This Period</th>
                            <th colspan="3">Cumulative Now</th>
                            <th rowspan="3">Status</th>
                        </tr>
                        <tr>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                            <th>Plan</th><th>Actual</th>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                        </tr>
                        <tr class="formula-header">
                            <th>A</th>
                            <th>B</th><th>C</th><th>C-B</th>
                            <th>D-B</th><th>E-C</th>
                            <th>D</th><th>E</th><th>E-D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentPeriod->epc as $index => $epc)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="text-align: left;">{{ $epc->description }}</td>
                                <td>{{ number_format($epc->weight_factor, 2) }}%</td>
                                <td>{{ number_format($epc->cum_last_plan, 2) }}%</td>
                                <td>{{ number_format($epc->cum_last_actual, 2) }}%</td>
                                <td>{{ number_format($epc->cum_last_variance, 2) }}%</td>
                                <td>{{ number_format($epc->this_plan, 2) }}%</td>
                                <td>{{ number_format($epc->this_actual, 2) }}%</td>
                                <td>{{ number_format($epc->cum_now_plan, 2) }}%</td>
                                <td>{{ number_format($epc->cum_now_actual, 2) }}%</td>
                                <td>{{ number_format($epc->cum_now_variance, 2) }}%</td>
                                <td>
                                    <span class="status-badge status-{{ $epc->status }}">
                                        {{ ucfirst($epc->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>Belum ada data EPC untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Engineering -->
<div class="section-card">
    <div class="section-header">Engineering Documents</div>
    <div class="section-content">
        @if($currentPeriod->engineerings && $currentPeriod->engineerings->count() > 0)
            <table class="progress-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Subkategori</th>
                        <th>Sub</th>
                        <th>Nomor Dokumen</th>
                        <th>Document Name</th>
                        <th>Approval Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentPeriod->engineerings as $index => $eng)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="text-align: left;">{{ $eng->kategori ?: '-' }}</td>
                            <td style="text-align: left;">{{ $eng->subkategori ?: '-' }}</td>
                            <td style="text-align: left;">{{ $eng->sub ?: '-' }}</td>
                            <td style="text-align: left;">
                                @php
                                    $docParts = collect([
                                        $eng->doc_part1,
                                        $eng->doc_part2,
                                        $eng->doc_part3,
                                        $eng->doc_part4,
                                        $eng->doc_part5,
                                        $eng->doc_part6
                                    ])->filter()->toArray();
                                @endphp
                                {{ implode('-', $docParts) ?: '-' }}
                            </td>
                            <td style="text-align: left;">{{ $eng->document_name ?: 'Engineering Document' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($eng->approval_status) }}">
                                    {{ ucfirst(strtolower($eng->approval_status)) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p>Belum ada data Engineering Documents untuk periode ini.</p>
            </div>
        @endif
    </div>
</div>

    <!-- Procurement Progress -->
    <div class="section-card">
        <div class="section-header">Procurement Material Progress</div>
        <div class="section-content">
            @if($currentPeriod->procurementMaterials && $currentPeriod->procurementMaterials->count() > 0)
                <table class="progress-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Nomor PO</th>
                            <th>PO Terbit</th>
                            <th>Submit TPG</th>
                            <th>Approve TPG</th>
                            <th>Manufacture</th>
                            <th>FAT</th>
                            <th>Delivery</th>
                            <th>Material On Site</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentPeriod->procurementMaterials as $material)
                            <tr>
                                <td style="text-align: left;">{{ $material->material_name }}</td>
                                <td>{{ $material->po_number }}</td>
                                <td>{{ $material->po_issued }}</td>
                                <td>{{ $material->submit_tpg }}</td>
                                <td>{{ $material->approve_tpg }}</td>
                                <td>{{ $material->manufacture }}</td>
                                <td>{{ $material->fat }}</td>
                                <td>{{ $material->delivery }}</td>
                                <td>{{ $material->material_on_site }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>Belum ada data Procurement Material untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Procurement Service Progress -->
    <div class="section-card">
        <div class="section-header">Procurement Service Progress</div>
        <div class="section-content">
            @if($currentPeriod->procurementServices && $currentPeriod->procurementServices->count() > 0)
                <table class="progress-table">
                    <thead>
                        <tr>
                            <th>Jasa</th>
                            <th>Nomor PO</th>
                            <th>PO Terbit</th>
                            <th>Mobilisasi</th>
                            <th>Pekerjaan Lapangan</th>
                            <th>Pelaporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentPeriod->procurementServices as $service)
                            <tr>
                                <td style="text-align: left;">{{ $service->service_name }}</td>
                                <td>{{ $service->po_number }}</td>
                                <td>{{ $service->po_issued }}</td>
                                <td>{{ $service->mobilization }}</td>
                                <td>{{ $service->field_work }}</td>
                                <td>{{ $service->reporting }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>Belum ada data Procurement Service untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Construction Progress -->
    <div class="section-card">
        <div class="section-header">Construction Progress</div>
        <div class="section-content">
            @if($currentPeriod->constructions && $currentPeriod->constructions->count() > 0)
                <table class="progress-table">
                    <thead>
                        <tr>
                            <th rowspan="3">No</th>
                            <th rowspan="3">Description</th>
                            <th rowspan="2">Weight Factor</th>
                            <th colspan="3">Cumulative Last</th>
                            <th colspan="2">This Period</th>
                            <th colspan="3">Cumulative Now</th>
                            <th rowspan="3">Status</th>
                        </tr>
                        <tr>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                            <th>Plan</th><th>Actual</th>
                            <th>Plan</th><th>Actual</th><th>Variance</th>
                        </tr>
                        <tr class="formula-header">
                            <th>A</th>
                            <th>B</th><th>C</th><th>C-B</th>
                            <th>D-B</th><th>E-C</th>
                            <th>D</th><th>E</th><th>E-D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentPeriod->constructions as $index => $construction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="text-align: left;">{{ $construction->description }}</td>
                                <td>{{ number_format($construction->weight_factor, 2) }}%</td>
                                <td>{{ number_format($construction->cum_last_plan, 2) }}%</td>
                                <td>{{ number_format($construction->cum_last_actual, 2) }}%</td>
                                <td>{{ number_format($construction->cum_last_variance, 2) }}%</td>
                                <td>{{ number_format($construction->this_plan, 2) }}%</td>
                                <td>{{ number_format($construction->this_actual, 2) }}%</td>
                                <td>{{ number_format($construction->cum_now_plan, 2) }}%</td>
                                <td>{{ number_format($construction->cum_now_actual, 2) }}%</td>
                                <td>{{ number_format($construction->cum_now_variance, 2) }}%</td>
                                <td>
                                    <span class="status-badge status-{{ $construction->status }}">
                                        {{ ucfirst($construction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>Belum ada data Construction untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
    
@else
    <div class="section-card">
        <div class="section-content">
            <div class="empty-state">
                <p>Proyek ini belum memiliki periode. Silakan buat periode terlebih dahulu.</p>
            </div>
        </div>
    </div>
@endif

<script>
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
</div>
@endsection