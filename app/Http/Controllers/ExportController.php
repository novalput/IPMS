<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Period;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\SimpleType\Jc;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        $projects = Project::with('periods')->orderBy('name')->get();
        return view('pages.export.index', compact('projects'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'periods' => 'required|array|min:1',
            'periods.*' => 'exists:periods,id',
            'template_type' => 'required|in:mingguan,bulanan',
            'format' => 'required|in:docx,pdf',
            'sections' => 'required|array|min:1',
            'sections.*' => 'in:epc,engineering,procurement_material,procurement_service,construction,activities,issues,documentation',
            'template_name' => 'nullable|string|max:100',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $periods = Period::whereIn('id', $validated['periods'])
                        ->where('project_id', $project->id)
                        ->with([
                            'epc',
                            'engineerings',
                            'procurementMaterials', 
                            'procurementServices',
                            'constructions',
                            'activities',
                            'issues',
                            'documentations' 
                        ])
                        ->orderBy('start_date')
                        ->get();

        // Generate logo configuration
        $logoConfig = $this->generateLogoConfig($project, $validated);

        $data = [
            'project' => $project,
            'periods' => $periods,
            'template_type' => $validated['template_type'],
            'sections' => $validated['sections'],
            'template_name' => $validated['template_name'] ?: "Laporan {$validated['template_type']} - {$project->name}",
            'generated_at' => Carbon::now('Asia/Jakarta')->format('d/m/Y H:i'),
            'logo_config' => $logoConfig
        ];

        if ($validated['format'] === 'docx') {
            return $this->exportDocxAlternative($data);
        } else {
            return $this->exportPdf($data);
        }
    }

    // Fungsi disederhanakan total
    private function generateLogoConfig()
    {
        $base = public_path('images');
        
        // Sesuaikan nama file logo kanan Anda di sini
        $leftLogoPath = $base . '/ipms-logo.svg';
        $rightLogoPath = $base . '/ipms-logo.svg';

        return [
            // Logo Kiri
            'ipms_logo_path' => $leftLogoPath,
            'ipms_logo_url'  => asset('images/ipms-logo.svg'),
            
            // Logo Kanan (Baru)
            'right_logo_path' => $rightLogoPath,
            'right_logo_url'  => asset('images/ipms-logo.svg'),
        ];
    }

    private function exportDocxAlternative($data)
    {
        $html = view('pages.export.docx-template', $data)->render();
        
        $filename = $this->generateFilename($data['project']->name, $data['template_type'], 'doc');
        
        $headers = [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response($html, 200, $headers);
    }

    private function exportPdf($data)
    {
        // debug tambahan — Tunjukkan binary, ini file, sapi, extension_dir, dan apakah gd ter-list
        \Log::info('PHP_BINARY: ' . PHP_BINARY);
        \Log::info('php_ini_loaded_file: ' . (php_ini_loaded_file() ?: 'none'));
        \Log::info('PHP_SAPI: ' . php_sapi_name());
        \Log::info('PHP_VERSION: ' . phpversion());
        \Log::info('extension_dir: ' . ini_get('extension_dir'));
        \Log::info('gd in get_loaded_extensions: ' . (in_array('gd', get_loaded_extensions()) ? 'yes' : 'no'));
        \Log::info('extension_loaded(gd): ' . (extension_loaded('gd') ? 'yes' : 'no'));
        \Log::info('function_exists(imagecreatefrompng): ' . (function_exists('imagecreatefrompng') ? 'yes' : 'no'));
        \Log::info('function_exists(imagecreatefromjpeg): ' . (function_exists('imagecreatefromjpeg') ? 'yes' : 'no'));
        // ...existing debug you already have...
        try {
            $gdInfo = function_exists('gd_info') ? gd_info() : null;
            \Log::info('gd_info: ' . ($gdInfo ? json_encode($gdInfo) : 'null'));
        } catch (\Throwable $e) {
            \Log::error('gd_info error: ' . $e->getMessage());
        }

        $pdf = Pdf::loadView('pages.export.pdf-template', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = $this->generateFilename($data['project']->name, $data['template_type'], 'pdf');

        return $pdf->download($filename);
    }

    private function generateFilename($projectName, $templateType, $format)
    {
        $cleanProjectName = preg_replace('/[^A-Za-z0-9\-]/', '_', $projectName);
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        
        return "Laporan_{$templateType}_{$cleanProjectName}_{$timestamp}.{$format}";
    }

    public function getPeriods(Request $request)
    {
        $projectId = $request->input('project_id');
        $periods = Period::where('project_id', $projectId)
                        ->orderBy('start_date')
                        ->get(['id', 'name', 'start_date', 'end_date'])
                        ->map(function($period) {
                            return [
                                'id' => $period->id,
                                'name' => $period->name,
                                'start_date' => $period->start_date ? $period->start_date->format('Y-m-d') : null,
                                'end_date' => $period->end_date ? $period->end_date->format('Y-m-d') : null,
                            ];
                        });
        
        return response()->json($periods);
    }

    public function preview(Request $request)
    {
        if (!$request->has(['project_id', 'periods', 'template_type', 'sections'])) {
            return response()->json(['error' => 'Data tidak lengkap']);
        }

        $project = Project::findOrFail($request->project_id);
        $periods = Period::whereIn('id', $request->periods)
                        ->where('project_id', $project->id)
                        ->with(['epc', 'engineerings', 'procurementMaterials', 'procurementServices', 'constructions', 'activities', 'issues'])
                        ->orderBy('start_date')
                        ->get();

        // Config sederhana untuk preview
        $logoConfig = $this->generateLogoConfig();

        $data = [
            'project' => $project,
            'periods' => $periods,
            'template_type' => $request->template_type,
            'sections' => $request->sections,
            'template_name' => $request->template_name ?: "Laporan {$request->template_type} - {$project->name}",
            'generated_at' => \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y H:i'),
            'logo_config' => $logoConfig
        ];

        $html = view('pages.export.preview', $data)->render();
        return response()->json(['html' => $html]);
    }
}