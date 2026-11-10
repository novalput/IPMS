<div class="preview-document">
    <table style="width: 100%; border-collapse: collapse; border: 5px solid #000; margin-bottom: 20px;">
        <tr>
            <td style="width: 140px; border: 5px solid #000; padding: 15px; text-align: left; vertical-align: middle;">
                @if(isset($logo_config['ipms_logo_path']) && file_exists($logo_config['ipms_logo_path']))
                    <img src="{{ $logo_config['ipms_logo_url'] }}" alt="IPMS Logo" style="max-height: 80px; width: auto; max-width: 140px; display: block;">
                @else
                    <div style="width: 120px; height: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc;">IPMS</div>
                @endif
            </td>

            <td style="border: 5px solid #000; padding: 10px; text-align: center; vertical-align: middle;">
                <h1 style="color:#000000; font-size:12pt; margin:0 0 4pt 0; line-height:1.15; font-weight:bold; text-transform:uppercase;">
                    INTEGRATED PROJECT MANAGEMENT SYSTEM
                </h1>
                <h1 style="color:#000000; font-size:12pt; margin:0 0 4pt 0; line-height:1.15; font-weight:bold; text-transform:uppercase; font-style: italic;">
                    INTEGRATED MANAGEMENT SYSTEM
                </h1>
                <h1 style="color:#000000; font-size:12pt; margin:0 0 4pt 0; line-height:1.15; font-weight:bold; text-transform:uppercase;">
                    2026
                </h1>
            </td>

            <td style="width: 140px; border: 5px solid #000; padding: 15px; text-align: right; vertical-align: middle;">
                @if(isset($logo_config['right_logo_path']) && file_exists($logo_config['right_logo_path']))
                    <img src="{{ $logo_config['right_logo_url'] }}" alt="Right Logo" style="max-height: 80px; width: auto; max-width: 140px; display: block; margin-left: auto;">
                @else
                    <div style="width: 120px; height: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc; margin-left: auto;">Logo Kanan</div>
                @endif
            </td>
        </tr>
    </table>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="margin-top: 0; color: #333; font-size: 14px;">INFORMASI PROYEK</h3>
        <p style="margin: 5px 0; color: #555;"><strong>Nama Proyek:</strong> {{ $project->name }}</p>
        <p style="margin: 5px 0; color: #555;"><strong>Deskripsi:</strong> {{ $project->description ?: 'Tidak ada deskripsi' }}</p>
        <p style="margin: 5px 0; color: #555;"><strong>Periode yang Diekspor:</strong> 
            @foreach($periods as $period)
                {{ $period->name }}@if(!$loop->last), @endif
            @endforeach
        </p>
    </div>

    <!-- Content for each period -->
    @foreach($periods as $period)
        <div style="margin-bottom: 25px;">
            <div style="background: #00AEEF; color: white; padding: 8px 12px; font-size: 13px; font-weight: bold; margin-bottom: 12px;">
                PERIODE: {{ $period->name }}
                @if($period->start_date && $period->end_date)
                    ({{ $period->start_date->format('Y-m-d') }} - {{ $period->end_date->format('Y-m-d') }})
                @endif
            </div>

            <!-- Description -->
            @if(in_array('description', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">Deskripsi Periode</div>
                <p style="font-size: 11px;">{{ $period->description ?: 'Tidak ada deskripsi untuk periode ini.' }}</p>
            @endif

            <!-- EPC Progress -->
            @if(in_array('epc', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">EPC PROGRESS</div>
                @if($period->epc->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Deskripsi</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Bobot</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Last Plan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Last Actual</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">This Plan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">This Actual</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Now Plan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Now Actual</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period->epc as $epc)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $epc->description }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $epc->weight_factor }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($epc->cum_last_plan ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($epc->cum_last_actual ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($epc->this_plan ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($epc->this_actual ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($epc->cum_now_plan ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($epc->cum_now_actual ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ ucfirst($epc->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada data EPC untuk periode ini</p>
                @endif
            @endif

            <!-- Engineering -->
            @if(in_array('engineering', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">ENGINEERING DOCUMENTS</div>
                @if($period->engineerings->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Kategori</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Subkategori</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Sub</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Nomor Dokumen</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Document Name</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period->engineerings as $eng)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $eng->kategori ?: '-' }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $eng->subkategori ?: '-' }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $eng->sub ?: '-' }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">
                                        {{ collect([$eng->doc_part1, $eng->doc_part2, $eng->doc_part3, $eng->doc_part4, $eng->doc_part5, $eng->doc_part6])->filter()->implode('-') }}
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $eng->document_name ?: 'Engineering Document' }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $eng->approval_status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada data engineering untuk periode ini</p>
                @endif
            @endif

            <!-- Procurement Material -->
            @if(in_array('procurement_material', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">PROCUREMENT MATERIAL</div>
                @if($period->procurementMaterials->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Material Name</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Nomor PO</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">PO Terbit</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Submit TPG</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Approve TPG</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Manufacture</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">FAT</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Delivery</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">On Site</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period->procurementMaterials as $material)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $material->material_name }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $material->po_number ?: '-' }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->po_issued }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->submit_tpg }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->approve_tpg }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->manufacture }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->fat }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->delivery }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $material->material_on_site }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada data procurement material untuk periode ini</p>
                @endif
            @endif

            <!-- Procurement Service -->
            @if(in_array('procurement_service', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">PROCUREMENT SERVICE</div>
                @if($period->procurementServices->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Service Name</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Nomor PO</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">PO Terbit</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Mobilisasi</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Pekerjaan Lapangan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period->procurementServices as $service)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $service->service_name }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $service->po_number ?: '-' }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $service->po_issued }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $service->mobilization }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $service->field_work }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $service->reporting }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada data procurement service untuk periode ini</p>
                @endif
            @endif

            <!-- Construction -->
            @if(in_array('construction', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">CONSTRUCTION PROGRESS</div>
                @if($period->constructions->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Deskripsi</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Bobot</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Last Plan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Last Actual</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">This Plan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">This Actual</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Now Plan</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Cum Now Actual</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period->constructions as $con)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $con->description }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $con->weight_factor }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($con->cum_last_plan ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($con->cum_last_actual ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($con->this_plan ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($con->this_actual ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($con->cum_now_plan ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ number_format($con->cum_now_actual ?? 0, 1) }}%</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ ucfirst($con->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada data construction untuk periode ini</p>
                @endif
            @endif

            <!-- Activities -->
            @if(in_array('activities', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">ACTIVITIES</div>
                @if($period->activities->count() > 0)
                    @php $activities = $period->activities->groupBy('scope') @endphp
                    @foreach($activities as $scope => $scopeActivities)
                        <h4 style="margin: 8px 0 4px 0; color: #333; font-size: 11px;">Scope {{ $scope }}</h4>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Lokasi</th>
                                    <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Pencapaian</th>
                                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Progress</th>
                                    <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Rencana</th>
                                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scopeActivities as $activity)
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 4px;">{{ $activity->location }}</td>
                                        <td style="border: 1px solid #ddd; padding: 4px;">{{ Str::limit($activity->achievement_this_week, 60) }}</td>
                                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $activity->progress }}%</td>
                                        <td style="border: 1px solid #ddd; padding: 4px;">{{ Str::limit($activity->plan_next_week, 60) }}</td>
                                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ ucfirst($activity->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada data activities untuk periode ini</p>
                @endif
            @endif

            <!-- Issues -->
            @if(in_array('issues', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">ISSUES</div>
                @if($period->issues->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Nama Issue</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Lokasi</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Equipment</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left;">Deskripsi</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period->issues as $issue)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $issue->name }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $issue->location }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $issue->equipment }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px;">{{ Str::limit($issue->description, 80) }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ ucfirst($issue->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada issues untuk periode ini</p>
                @endif
            @endif

            @if(in_array('documentation', $sections))
                <div style="color: #333; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd;">DOKUMENTASI FOTO</div>
                @if($period->documentations->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 15px 0;">
                        @foreach($period->documentations->take(8) as $doc) {{-- Limit preview to 8 photos --}}
                            
                            @php
                                // 1. Bersihkan path untuk pengecekan fisik (exists)
                                // DB: /storage/images/doc.jpg -> Clean: images/doc.jpg
                                $cleanPath = str_replace('/storage/', '', $doc->file_path);
                            @endphp

                            <div style="text-align: center; border: 1px solid #eee; border-radius: 8px; padding: 10px; background: #fafafa;">
                                {{-- 2. Cek fisik menggunakan path bersih --}}
                                @if($doc->file_path && \Storage::disk('public')->exists($cleanPath))
                                    {{-- 3. Tampilkan gambar menggunakan asset() karena path DB sudah lengkap (/storage/...) --}}
                                    <img src="{{ asset($doc->file_path) }}" 
                                         style="max-width: 100%; max-height: 120px; border-radius: 4px; object-fit: cover;" 
                                         alt="{{ $doc->title }}">
                                @else
                                    <div style="width: 100%; height: 120px; background: #f0f0f0; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                        <span style="color: #999; font-size: 10px;">Foto tidak tersedia</span>
                                    </div>
                                @endif
                                <div style="margin-top: 8px;">
                                    <div style="font-weight: bold; font-size: 10px; color: #333; margin-bottom: 3px;">
                                        {{ $doc->title ?: 'Tanpa Judul' }}
                                    </div>
                                    @if($doc->description)
                                        <div style="font-size: 9px; color: #666; line-height: 1.2;">
                                            {{ Str::limit($doc->description, 60) }}
                                        </div>
                                    @endif
                                    <div style="font-size: 8px; color: #999; margin-top: 4px;">
                                        {{ $doc->created_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($period->documentations->count() > 8)
                        <p style="text-align: center; color: #666; font-style: italic; font-size: 10px; margin-top: 10px;">
                            ... dan {{ $period->documentations->count() - 8 }} foto lainnya akan ditampilkan di export lengkap
                        </p>
                    @endif
                @else
                    <p style="text-align: center; color: #666; font-style: italic; padding: 15px; font-size: 11px;">Tidak ada dokumentasi foto untuk periode ini</p>
                @endif
            @endif
        </div>

        @if(!$loop->last)
            <div style="border-top: 2px dashed #ddd; margin: 20px 0; padding-top: 15px;">
                <small style="color: #999; font-style: italic;">--- Halaman Berikutnya ---</small>
            </div>
        @endif
    @endforeach

    <!-- Approval Table Preview - DIPERBAIKI -->
    <div style="margin-top: 30px; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 11px; border: 1px solid #000;">
            <!-- Baris 1: Header -->
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; background-color: #f8f9fa; font-weight: bold; width: 16.66%;">APPROVED BY</th>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; background-color: #f8f9fa; font-weight: bold; width: 33.33%;" colspan="2">CHECKED BY</th>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; background-color: #f8f9fa; font-weight: bold; width: 50%;" colspan="3">TESTED BY</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris 2: Jabatan dengan 6 kolom -->
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-style: italic; color: #666;">Project Manager</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-style: italic; color: #666;">Site Manager</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-style: italic; color: #666;">Engineering Manager</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-style: italic; color: #666;">Construction Manager</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-style: italic; color: #666;">Quality Control</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-style: italic; color: #666;">Safety Officer</td>
                </tr>
                
                <!-- Baris 3: Area tanda tangan dengan 6 kolom (tinggi) -->
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; height: 80px; background-color: #fafafa;"></td>
                    <td style="border: 1px solid #000; padding: 8px; height: 80px; background-color: #fafafa;"></td>
                    <td style="border: 1px solid #000; padding: 8px; height: 80px; background-color: #fafafa;"></td>
                    <td style="border: 1px solid #000; padding: 8px; height: 80px; background-color: #fafafa;"></td>
                    <td style="border: 1px solid #000; padding: 8px; height: 80px; background-color: #fafafa;"></td>
                    <td style="border: 1px solid #000; padding: 8px; height: 80px; background-color: #fafafa;"></td>
                </tr>
                
                <!-- Baris 4: Nama terang dengan 6 kolom -->
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10px;">( _____________ )</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10px;">( _____________ )</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10px;">( _____________ )</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10px;">( _____________ )</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10px;">( _____________ )</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10px;">( _____________ )</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
        <p>Generated on {{ $generated_at }} | Integrated Project Management System Suralaya - Project Management System</p>
    </div>
</div>