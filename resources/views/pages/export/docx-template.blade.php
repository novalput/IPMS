<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>{{ $template_name }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>90</w:Zoom>
            <w:DoNotPromptForConvert/>
            <w:DoNotShowRevisions/>
            <w:DoNotPrintRevisions/>
            <w:DoNotShowMarkup/>
            <w:DoNotShowComments/>
            <w:DoNotShowInsertionsAndDeletions/>
            <w:DoNotShowPropertyChanges/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        /* Set page margins untuk Word: NARROW (1.27cm) */
        @page {
            size: A4;
            margin: 1.27cm 1.27cm 1.27cm 1.27cm;
        }

        /* Body */
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Word Section margins: NARROW (1.27cm) */
        div.WordSection1 {
            page: WordSection1;
            margin: 1.27cm 1.27cm 1.27cm 1.27cm;
            padding: 0;
        }

        /* Header Layout - Improved untuk Word */        
        table.header-with-logos {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    table-layout: fixed;
    margin-bottom: 20px;
}

/* Tinggi baris */
tr.header-row {
    height: 120px;
}

/* BORDER HANYA DI DALAM */
td.logo-left,
td.header-center,
td.logo-right {
    border: 2px solid #000;
    border-collapse: collapse;
    border-spacing: 0;
    vertical-align: middle;
    padding: 8px;
}

        td.logo-left, .logo-right {
            width: 140px;
            text-align: center;
        }

        /* HEADER TENGAH */
td.header-center {
    text-align: center;
    padding: 8px 16px;
}

        /* Logo Styling - ukuran spesifik untuk Word (cm) */
        .logo-img {
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 120px;
            height: 80px;
            background: #f0f0f0;
            display: inline-block;
            text-align: center;
            line-height: 20px;
            font-size: 8px;
            color: #666;
            border: 1px dashed #ccc;
            vertical-align: middle;
            padding: 10px 5px;
            box-sizing: border-box;
            margin: 0 auto;
        }

        /* Header Text Styling - sama dengan PDF */
        .header-center h1 {
            color: #000080;
            font-size: 18px;
            margin: 0 0 8px 0;
            line-height: 1.1;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-center h2 {
            color: #000060;
            font-size: 14px;
            margin: 0 0 8px 0;
            line-height: 1.1;
            font-weight: 600;
            text-transform: uppercase;
        }

        .header-center h3 {
            color: #000060;
            font-size: 12px;
            margin: 0;
            line-height: 1.1;
            font-weight: 500;
        }
        
        .project-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .project-info h3 {
            margin-top: 0;
            color: #333;
            font-size: 14px;
        }
        
        .project-info p {
            margin: 5px 0;
            color: #555;
            font-size: 10px;
        }
        
        .period-section {
            margin-bottom: 25px;
        }
        
        .period-title {
            background: #00AEEF;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            page-break-before: always;
        }
        
        /* Khusus periode pertama, jangan page break */
        .period-title.first-period {
            page-break-before: auto;
        }
        
        .section-title {
            color: #333;
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
            /* Untuk Word compatibility */
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            /* Word specific */
            mso-line-height-rule: exactly;
        }
        
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            font-size: 9px;
        }
        
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 15px;
            font-size: 11px;
        }
        
        .text-center { text-align: center; }
        .formula-row { font-size: 7px; color: #002e8f; }
        
        /* Responsive column widths - sama seperti PDF */
        .col-no { width: 4%; }
        .col-desc { width: 25%; }
        .col-weight { width: 8%; }
        .col-percent { width: 7%; }
        .col-status { width: 8%; }
        .col-category { width: 12%; }
        .col-doc-num { width: 20%; }
        .col-doc-name { width: 30%; }
        .col-material { width: 15%;  }
        .col-proc-step { width: 10%; }
        .col-location { width: 15%; }
        .col-achievement { width: 30%; }
        .col-plan { width: 25%; }
        .col-issue-name { width: 18%; }
        .col-equipment { width: 15%; }
        .col-description { width: 30%; }
        
        /* Status badges - sama seperti PDF */
        .status-badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-ongoing { background: #fff3cd; color: #856404; }
        .status-done { background: #d4edda; color: #155724; }
        .status-delayed { background: #f8d7da; color: #721c24; }
        
        /* Scope titles untuk activities */
        .scope-title {
            margin: 8px 0 4px 0;
            color: #333;
            font-size: 11px;
            font-weight: bold;
        }
        
        /* Period description */
        .period-description {
            font-size: 11px;
            margin: 5px 0;
        }
        
        /* Approval table styles - DIPERBAIKI */
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0 20px 0;
            font-size: 11px;
            page-break-inside: avoid;
            border: 1px solid #000;
        }

        .approval-table th, .approval-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }

        .approval-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            height: 30px;
        }

        .approval-table .position-row {
            height: 30px;
            font-style: italic;
            color: #666;
            font-size: 10px;
        }

        .approval-table .signature-row {
            height: 80px;
            background-color: #fafafa;
        }

        .approval-table .name-row {
            height: 30px;
            font-weight: bold;
            font-size: 10px;
        }

        /* Approval table structure yang benar */
        .approval-table .col-1 {
            width: 16.66%;
        }

        .approval-table .col-2-3 {
            width: 33.33%;
        }

        .approval-table .col-4-6 {
            width: 50%;
        }
        
        /* Word-specific page settings */
        @page WordSection1 {
            size: 21.0cm 29.7cm; /* A4 */
            margin: 1.27cm 1.27cm 1.27cm 1.27cm;
            mso-header-margin: 1.27cm;
            mso-footer-margin: 1.27cm;
            mso-paper-source: 0;
        }

        /* Header table (tanpa border, beri garis biru di bawah) */
        .header-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse; /* Penting agar border menyatu */
            margin-bottom: 20pt;
        }

        
        .header-table tr,
        .header-table td {
            vertical-align: middle;
            padding: 0.2cm;
        }
        .td-left { text-align: left; }
        .td-center { text-align: center; }
        .td-right { text-align: right; }

        /* Header text: font-size dan warna biru sesuai permintaan */
        .header-center h1 {
            color: #000000 !important;
            font-size: 11pt !important;
            margin: 0 0 4pt 0;
            line-height: 1.15;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-center h2 {
            color: #000000 !important;
            font-size: 10.5pt !important;
            margin: 0 0 4pt 0;
            line-height: 1.1;
            font-weight: 600;
            text-transform: uppercase;
        }
        .header-center h3 {
            color: #000000 !important;
            font-size: 10pt !important;
            margin: 0;
            line-height: 1.1;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Word Section Start -->
    <div class="WordSection1">

        @php
            // Hitung pixel untuk width/height attribute gambar agar Word tidak bingung
            // 140px kolom, gambar max 120px
            // Kita pakai estimasi cm untuk inline style width gambar agar konsisten
            $logoWidthCm = 3; 
            $cmToPx = 37.795;
            $ipmsWidthPx = (int) round($logoWidthCm * $cmToPx);
            $ipmsHeightPx = (int) round(1.2 * $cmToPx);
        @endphp

        <table class="header-with-logos">
    <tr class="header-row">

        {{-- LOGO KIRI --}}
        <td class="logo-left">
            @if(isset($logo_config['ipms_logo_path']) && file_exists($logo_config['ipms_logo_path']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logo_config['ipms_logo_path'])) }}"
                     alt="IPMS Logo"
                     width="{{ $ipmsWidthPx }}"
                     height="{{ $ipmsHeightPx }}"
                     class="logo-img"
                     style="width:3cm;">
            @else
                <div>IPMS</div>
            @endif
        </td>

        {{-- HEADER TENGAH --}}
        <td class="header-center">
            <h1>INTEGRATED PROJECT MANAGEMENT SYSTEM</h1>
            <h1 style="font-style: italic;">INTEGRATED MANAGEMENT SYSTEM</h1>
            <h1 style="font-weight: bold;">2026</h1>
        </td>

        {{-- LOGO KANAN --}}
        <td class="logo-right">
            @if(isset($logo_config['right_logo_path']) && file_exists($logo_config['right_logo_path']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logo_config['right_logo_path'])) }}"
                     alt="Right Logo"
                     width="{{ $ipmsWidthPx }}"
                     height="{{ $ipmsHeightPx }}"
                     class="logo-img"
                     style="width:3cm;">
            @else
                <div>Logo</div>
            @endif
        </td>

    </tr>
</table>



        <div class="project-info">
            <h3>INFORMASI PROYEK</h3>
            <p><strong>Nama Proyek:</strong> {{ $project->name }}</p>
            <p><strong>Deskripsi:</strong> {{ $project->description ?: 'Tidak ada deskripsi' }}</p>
            <p><strong>Periode yang Diekspor:</strong> 
                @foreach($periods as $period)
                    {{ $period->name }}@if(!$loop->last), @endif
                @endforeach
            </p>
        </div>

        <!-- Content for each period -->
        @foreach($periods as $period)
            <div class="period-section">
                <div class="period-title {{ $loop->first ? 'first-period' : '' }}">
                    PERIODE: {{ $period->name }}
                    @if($period->start_date && $period->end_date)
                        ({{ $period->start_date->format('Y-m-d') }} - {{ $period->end_date->format('Y-m-d') }})
                    @endif
                </div>

                <!-- Description -->
                @if(in_array('description', $sections))
                    <div class="section-title">Deskripsi Periode</div>
                    <p class="period-description">{{ $period->description ?: 'Tidak ada deskripsi untuk periode ini.' }}</p>
                @endif

                <!-- EPC Progress -->
                @if(in_array('epc', $sections))
                    <div class="section-title">EPC PROGRESS</div>
                    @if($period->epc->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th rowspan="3" class="col-no">No</th>
                                    <th rowspan="3" class="col-desc">Deskripsi</th>
                                    <th rowspan="2" class="col-weight">Weight Factor</th>
                                    <th colspan="3" class="text-center">Cumulative Last</th>
                                    <th colspan="2" class="text-center">This Period</th>
                                    <th colspan="3" class="text-center">Cumulative Now</th>
                                    <th rowspan="3" class="col-status">Status</th>
                                </tr>
                                <tr>
                                    <th class="col-percent">Plan</th>
                                    <th class="col-percent">Actual</th>
                                    <th class="col-percent">Variance</th>
                                    <th class="col-percent">Plan</th>
                                    <th class="col-percent">Actual</th>
                                    <th class="col-percent">Plan</th>
                                    <th class="col-percent">Actual</th>
                                    <th class="col-percent">Variance</th>
                                </tr>
                                <tr class="formula-row">
                                    <th>A</th>
                                    <th>B</th><th>C</th><th>C-B</th>
                                    <th>D-B</th><th>E-C</th>
                                    <th>D</th><th>E</th><th>E-D</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->epc as $epc)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $epc->description }}</td>
                                        <td class="text-center">{{ $epc->weight_factor }}</td>
                                        <td class="text-center">{{ number_format($epc->cum_last_plan ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->cum_last_actual ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->cum_last_variance ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->this_plan ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->this_actual ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->cum_now_plan ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->cum_now_actual ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($epc->cum_now_variance ?? 0, 1) }}%</td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $epc->status }}">
                                                {{ ucfirst($epc->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data EPC untuk periode ini</div>
                    @endif
                @endif

                <!-- Engineering -->
                @if(in_array('engineering', $sections))
                    <div class="section-title">ENGINEERING DOCUMENTS</div>
                    @if($period->engineerings->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th class="col-category">Kategori</th>
                                    <th class="col-category">Subkategori</th>
                                    <th class="col-weight">Sub</th>
                                    <th class="col-doc-num">Nomor Dokumen</th>
                                    <th class="col-doc-name">Document Name</th>
                                    <th class="col-status">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->engineerings as $eng)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $eng->kategori ?: '-' }}</td>
                                        <td>{{ $eng->subkategori ?: '-' }}</td>
                                        <td>{{ $eng->sub ?: '-' }}</td>
                                        <td>
                                            {{ collect([$eng->doc_part1, $eng->doc_part2, $eng->doc_part3, $eng->doc_part4, $eng->doc_part5, $eng->doc_part6])->filter()->implode('-') }}
                                        </td>
                                        <td>{{ $eng->document_name ?: 'Engineering Document' }}</td>
                                        <td class="text-center">{{ $eng->approval_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data engineering untuk periode ini</div>
                    @endif
                @endif

                <!-- Procurement Material -->
                @if(in_array('procurement_material', $sections))
                    <div class="section-title">PROCUREMENT MATERIAL</div>
                    @if($period->procurementMaterials->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;" class="col-material">Material</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-material">Nomor PO</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">PO Terbit</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">Submit TPG</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">Approve TPG</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">Manufacture</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">FAT</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">Delivery</th>
                                    <th style="text-align: center; vertical-align: middle;" class="col-proc-step">Material On Site</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->procurementMaterials as $material)
                                    <tr>
                                        <td>{{ $material->material_name }}</td>
                                        <td class="text-center">{{ $material->po_number ?: '-' }}</td>
                                        <td class="text-center">{{ $material->po_issued }}</td>
                                        <td class="text-center">{{ $material->submit_tpg }}</td>
                                        <td class="text-center">{{ $material->approve_tpg }}</td>
                                        <td class="text-center">{{ $material->manufacture }}</td>
                                        <td class="text-center">{{ $material->fat }}</td>
                                        <td class="text-center">{{ $material->delivery }}</td>
                                        <td class="text-center">{{ $material->material_on_site }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data procurement material untuk periode ini</div>
                    @endif
                @endif

                <!-- Procurement Service -->
                @if(in_array('procurement_service', $sections))
                    <div class="section-title">PROCUREMENT SERVICE</div>
                    @if($period->procurementServices->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;" class="col-service">Jasa</th>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;" class="col-material">PO Number</th>
                                    <th style="width: 15%; text-align: center; vertical-align: middle;" class="col-material">PO Terbit</th>
                                    <th style="width: 15%; text-align: center; vertical-align: middle;" class="col-material">Mobilisasi</th>
                                    <th style="width: 15%; text-align: center; vertical-align: middle;" class="col-material">Pekerjaan Lapangan</th>
                                    <th style="width: 15%; text-align: center; vertical-align: middle;" class="col-material">Laporan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->procurementServices as $service)
                                    <tr>
                                        <td>{{ $service->service_name }}</td>
                                        <td class="text-center">{{ $service->po_number ?: '-' }}</td>
                                        <td class="text-center">{{ $service->po_issued }}</td>
                                        <td class="text-center">{{ $service->mobilization }}</td>
                                        <td class="text-center">{{ $service->field_work }}</td>
                                        <td class="text-center">{{ $service->reporting }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data procurement service untuk periode ini</div>
                    @endif
                @endif

                <!-- Construction -->
                @if(in_array('construction', $sections))
                    <div class="section-title">CONSTRUCTION PROGRESS</div>
                    @if($period->constructions->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th rowspan="3" class="col-no">No</th>
                                    <th rowspan="3" class="col-desc">Deskripsi</th>
                                    <th rowspan="2" class="col-weight">Weight Factor</th>
                                    <th colspan="3" class="text-center">Cumulative Last</th>
                                    <th colspan="2" class="text-center">This Period</th>
                                    <th colspan="3" class="text-center">Cumulative Now</th>
                                    <th rowspan="3" class="col-status">Status</th>
                                </tr>
                                <tr>
                                    <th class="col-percent">Plan</th>
                                    <th class="col-percent">Actual</th>
                                    <th class="col-percent">Variance</th>
                                    <th class="col-percent">Plan</th>
                                    <th class="col-percent">Actual</th>
                                    <th class="col-percent">Plan</th>
                                    <th class="col-percent">Actual</th>
                                    <th class="col-percent">Variance</th>
                                </tr>
                                <tr class="formula-row">
                                    <th>A</th>
                                    <th>B</th><th>C</th><th>C-B</th>
                                    <th>D-B</th><th>E-C</th>
                                    <th>D</th><th>E</th><th>E-D</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->constructions as $con)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $con->description }}</td>
                                        <td class="text-center">{{ $con->weight_factor }}</td>
                                        <td class="text-center">{{ number_format($con->cum_last_plan ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->cum_last_actual ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->cum_last_variance ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->this_plan ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->this_actual ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->cum_now_plan ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->cum_now_actual ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ number_format($con->cum_now_variance ?? 0, 1) }}%</td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $con->status }}">
                                                {{ ucfirst($con->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data construction untuk periode ini</div>
                    @endif
                @endif

                <!-- Activities -->
                @if(in_array('activities', $sections))
                    <div class="section-title">ACTIVITIES</div>
                    @if($period->activities->count() > 0)
                        @php $activities = $period->activities->groupBy('scope') @endphp
                        @foreach($activities as $scope => $scopeActivities)
                            <div class="scope-title">Scope {{ $scope }}</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="col-no">No</th>
                                        <th class="col-location">Lokasi</th>
                                        <th class="col-achievement">Pencapaian</th>
                                        <th class="col-percent">Progress</th>
                                        <th class="col-plan">Rencana</th>
                                        <th class="col-status">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scopeActivities as $activity)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $activity->location }}</td>
                                            <td>{{ $activity->achievement_this_week }}</td>
                                            <td class="text-center">{{ $activity->progress }}%</td>
                                            <td>{{ $activity->plan_next_week }}</td>
                                            <td class="text-center">
                                                <span class="status-badge status-{{ $activity->status }}">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @else
                        <div class="no-data">Tidak ada data activities untuk periode ini</div>
                    @endif
                @endif

                <!-- Issues -->
                @if(in_array('issues', $sections))
                    <div class="section-title">ISSUES</div>
                    @if($period->issues->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th class="col-issue-name">Nama Issue</th>
                                    <th class="col-location">Lokasi</th>
                                    <th class="col-equipment">Equipment</th>
                                    <th class="col-description">Deskripsi</th>
                                    <th class="col-status">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->issues as $issue)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $issue->name }}</td>
                                        <td>{{ $issue->location }}</td>
                                        <td>{{ $issue->equipment }}</td>
                                        <td>{{ $issue->description }}</td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $issue->status }}">
                                                {{ ucfirst($issue->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada issues untuk periode ini</div>
                    @endif
                @endif

                <!-- Documentation - Layout 2x2 per halaman -->
                @if(in_array('documentation', $sections))
                    <div class="section-title">DOKUMENTASI FOTO</div>
                    @if($period->documentations->count() > 0)
                        @php 
                            $docs = $period->documentations->chunk(4); // 4 foto per halaman
                            
                            // Konfigurasi ukuran foto untuk layout 2x2
                            $photoWidthCm = 8.5; // Lebar foto untuk 2 kolom (sedikit lebih kecil dari setengah 18.46)
                            $photoHeightCm = 5.5; // Height proporsional
                            $cmToPx = 37.795; // konversi cm ke pixel
                            $photoWidthPx = (int) round($photoWidthCm * $cmToPx);
                            $photoHeightPx = (int) round($photoHeightCm * $cmToPx);
                        @endphp
                        @foreach($docs as $chunkIndex => $docChunk)
                            @if($chunkIndex > 0)
                                <div style="page-break-before: always;"></div>
                            @endif
                            
                            <!-- Tabel 2x2 untuk 4 foto -->
                            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: none;">
                                @for($row = 0; $row < 2; $row++)
                                    <tr>
                                        @for($col = 0; $col < 2; $col++)
                                            @php
                                                $docIndex = ($row * 2) + $col;
                                                $doc = $docChunk->get($docIndex);
                                            @endphp
                                            <td style="width: 50%; padding: 10px; vertical-align: top; text-align: center; border: none;">
                                                @if($doc)
                                                    <!-- Container foto -->
                                                    <div style="text-align: center; margin-bottom: 15px;">
                                                        @php
                                                            // 1. Ambil path mentah dari database (contoh: /storage/images/documentation/foto.jpg)
                                                            $dbPath = $doc->file_path;
                                                            
                                                            // 2. Bersihkan '/storage/' agar menjadi relative path (contoh: images/documentation/foto.jpg)
                                                            // Ini meniru logika private function getStoragePath() di Controller Anda
                                                            $cleanPath = str_replace('/storage/', '', $dbPath);
                                                            
                                                            // 3. Gabungkan dengan path internal server Laravel
                                                            $fullPath = storage_path('app/public/' . $cleanPath);

                                                            // Debugging (Opsional: Matikan jika sudah live)
                                                            // dd($fullPath); 
                                                        @endphp

                                                        @if($doc->file_path && file_exists($fullPath))
                                                            @php
                                                                // 4. Deteksi ekstensi (JPG/PNG)
                                                                $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                                                                $mimeType = 'image/jpeg'; // Default
                                                                
                                                                if(strtolower($extension) === 'png') {
                                                                    $mimeType = 'image/png';
                                                                }

                                                                // 5. Encode gambar ke Base64
                                                                $imgData = base64_encode(file_get_contents($fullPath));
                                                                $src = 'data:' . $mimeType . ';base64,' . $imgData;
                                                            @endphp

                                                            <img src="{{ $src }}" 
                                                                width="{{ $photoWidthPx }}"
                                                                height="{{ $photoHeightPx }}"
                                                                style="width: {{ $photoWidthCm ?? 8.5 }}cm; height: {{ $photoHeightCm ?? 5.5 }}cm; border: 1px solid #ddd; border-radius: 4px; object-fit: cover; display: block; margin: 0 auto;" 
                                                                alt="{{ $doc->title }}">
                                                        @else
                                                            <div style="width: {{ $photoWidthPx }}px; height: {{ $photoHeightPx }}px; background: #f5f5f5; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; margin: 0 auto; border-radius: 4px; text-align:center;">
                                                                <span style="color: #dc3545; font-size: 10px;">
                                                                    File Fisik Hilang<br>
                                                                    <small style="font-size:8px; color:#999">{{ basename($doc->file_path) }}</small>
                                                                </span>
                                                            </div>
                                                        @endif
                                                            
                                                        <!-- Info foto -->
                                                        <div style="margin-top: 10px; padding: 0 5px; max-width: {{ $photoWidthCm }}cm; margin-left: auto; margin-right: auto;">
                                                            <div style="font-weight: bold; font-size: 11px; color: #333; margin-bottom: 4px; text-align: center; line-height: 1.2; word-wrap: break-word;">
                                                                {{ $doc->title ?: 'Tanpa Judul' }}
                                                            </div>
                                                            @if($doc->description)
                                                                <div style="font-size: 9px; color: #666; line-height: 1.3; text-align: center; word-wrap: break-word; margin-bottom: 4px;">
                                                                    {{ $doc->description }}
                                                                </div>
                                                            @endif
                                                            <div style="font-size: 8px; color: #999; text-align: center;">
                                                                {{ $doc->created_at->format('d M Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Empty cell jika tidak ada foto -->
                                                    <div style="height: {{ $photoHeightCm + 2 }}cm;"></div>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </table>
                        @endforeach
                    @else
                        <div class="no-data">Tidak ada dokumentasi foto untuk periode ini</div>
                    @endif
                @endif
            </div>
        @endforeach

        <!-- Approval Table - Default Table Style -->
        <table style="width:100%; border-collapse:collapse; margin:30px 0 20px 0; font-size:11px;">
            <thead>
                <tr style="background-color:#f8f9fa;">
                    <th style="border:1px solid #ddd; padding:8px; text-align:center;">APPROVED BY</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:center;" colspan="2">CHECKED BY</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:center;" colspan="3">TESTED BY</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-style:italic; color:#666;">Project Manager</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-style:italic; color:#666;">Site Manager</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-style:italic; color:#666;">Engineering Manager</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-style:italic; color:#666;">Construction Manager</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-style:italic; color:#666;">Quality Control</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-style:italic; color:#666;">Safety Officer</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; padding:8px; height:80px; background:#fafafa;"></td>
                    <td style="border:1px solid #ddd; padding:8px; height:80px; background:#fafafa;"></td>
                    <td style="border:1px solid #ddd; padding:8px; height:80px; background:#fafafa;"></td>
                    <td style="border:1px solid #ddd; padding:8px; height:80px; background:#fafafa;"></td>
                    <td style="border:1px solid #ddd; padding:8px; height:80px; background:#fafafa;"></td>
                    <td style="border:1px solid #ddd; padding:8px; height:80px; background:#fafafa;"></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; font-size:10px;">( _____________ )</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; font-size:10px;">( _____________ )</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; font-size:10px;">( _____________ )</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; font-size:10px;">( _____________ )</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; font-size:10px;">( _____________ )</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; font-size:10px;">( _____________ )</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
            <p>Generated on {{ $generated_at }} WIB | Integrated Project Management System</p>
        </div>
    </div>
    <!-- Word Section End -->
</body>
</html>