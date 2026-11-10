<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Period;
use App\Models\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ProjectProgress;

class ProjectController extends Controller
{
    public function getProject()
    {
        $projects = Project::with('periods')->get();
    }
    public function index()
    {
        $projects = Project::all();
        return view('pages.project.index', compact('projects'));
    }

    public function create()
    {
        // Izinkan admin dan user
        if (!auth()->user()) {
            return redirect()->route('login');
        }
        $projects = Project::all();
        $periods = Period::all();
        return view('pages.project.create', compact('projects', 'periods'));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'user'])) {
            return redirect()->route('new_project.index')->with('error', 'Tidak memiliki akses.');
        }

        $validated = $request->validate([
            'existing_project_id'   => 'nullable|exists:projects,id',
            'project_name'          => 'required|string|max:150',
            'description'           => 'nullable|string',
            'existing_period_id'    => 'nullable|exists:periods,id',
            'period_name'           => 'required|string|max:150',
            'start_date'            => 'nullable|date',
            'end_date'              => 'nullable|date|after_or_equal:start_date',
            
            // TAMBAHAN: Progress validation
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'weight' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'nullable|string',
        ]);

        $action = $request->input('period_action','save');
        $status = 'saved';
        if ($action === 'submit') {
            $status = 'submitted';
        }

        // Handle Project
        if ($request->filled('existing_project_id')) {
            $project = Project::find($request->existing_project_id);
        } else {
            $project = Project::create([
                'name' => $request->project_name,
                'description' => $request->description,
            ]);
        }

        // Handle Period
        if ($request->filled('existing_period_id')) {
            $period = Period::find($request->existing_period_id);
            if (!$period->canBeEditedBy(auth()->user())) {
                return back()->with('error', 'Akses ditolak.');
            }
            
            $period->update([
                'name' => $request->period_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'updated_by' => auth()->id(),
                'status' => $status,
            ]);

            $period->epc()->delete();
            $period->engineerings()->delete();
            $period->procurementMaterials()->delete();
            $period->procurementServices()->delete();
            $period->constructions()->delete();
            $period->activities()->delete();
            $period->issues()->delete();
        } else {
            $period = Period::create([
                'project_id' => $project->id,
                'name' => $request->period_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'status' => $status,
            ]);
        }

        // Update project description
        if ($request->filled('description') && !$request->filled('existing_project_id')) {
            $project->description = $request->input('description');
            $project->save();
        }

        // Save all details
        $this->saveDetails($period, $request);
        
        // TAMBAHAN: Save project progress
        $this->saveProjectProgress($period, $request);

        $message = $request->filled('existing_project_id')
            ? "Periode '{$period->name}' berhasil disimpan (status: {$period->status})."
            : "Proyek '{$project->name}' dan periode '{$period->name}' berhasil dibuat (status: {$period->status}).";

        return redirect()->route('new_project.index')->with('success', $message);
    }

    private function saveDetails($period, $request)
    {
        // Implementasi penyimpanan detail period
        $epcData = [];
        if ($request->has('epc')) {
            foreach ($request->epc as $epc) {
                // Skip jika description kosong (kecuali untuk total row)
                if (empty($epc['description']) && !isset($epc['is_total'])) continue;

                $epcRow = $period->epc()->create([
                    'period_id'         => $period->id,
                    'description'       => $epc['description'] ?? '',
                    'weight_factor'     => $epc['weight_factor'] ?? 0,
                    'cum_last_plan'     => $epc['cum_last_plan'] ?? 0,
                    'cum_last_actual'   => $epc['cum_last_actual'] ?? 0,
                    'cum_last_variance' => $epc['cum_last_variance'] ?? 0,
                    'this_plan'         => $epc['this_plan'] ?? 0,
                    'this_actual'       => $epc['this_actual'] ?? 0,
                    'cum_now_plan'      => $epc['cum_now_plan'] ?? 0,
                    'cum_now_actual'    => $epc['cum_now_actual'] ?? 0,
                    'cum_now_variance'  => $epc['cum_now_variance'] ?? 0,
                    'status'            => $epc['status'] ?? 'on schedule',
                    'is_total'          => isset($epc['is_total']) ? true : false,
                ]);
                
                // Store for progress tracking
                $epcData[] = $epc;
            }
        }

        // 5. Simpan data Engineering
        if ($request->has('engineering')) {
            foreach ($request->engineering as $eng) {
                // Skip jika semua field dokumen kosong
                if (empty($eng['doc_part1']) && empty($eng['doc_part2']) && empty($eng['doc_part3']) &&
                    empty($eng['doc_part4']) && empty($eng['doc_part5']) && empty($eng['doc_part6']) &&
                    empty($eng['document_name'])) {
                    continue;
                }

                $period->engineerings()->create([
                    'period_id'        => $period->id,
                    'kategori'         => $eng['kategori'] ?? null,
                    'subkategori'      => $eng['subkategori'] ?? null,
                    'sub'              => $eng['sub'] ?? null,
                    'doc_part1'        => $eng['doc_part1'] ?? null,
                    'doc_part2'        => $eng['doc_part2'] ?? null,
                    'doc_part3'        => $eng['doc_part3'] ?? null,
                    'doc_part4'        => $eng['doc_part4'] ?? null,
                    'doc_part5'        => $eng['doc_part5'] ?? null,
                    'doc_part6'        => $eng['doc_part6'] ?? null,
                    'document_name'    => $eng['document_name'] ?? null,
                    'approval_status'  => $eng['approval_status'] ?? 'ongoing',
                ]);
            }
        }

        // 6. Simpan data Procurement Material
        if ($request->has('material')) {
            foreach ($request->material as $mat) {
                // Skip jika material_name kosong
                if (empty($mat['material_name'])) continue;

                $period->procurementMaterials()->create([
                    'period_id'         => $period->id,
                    'material_name'     => $mat['material_name'] ?? '',
                    'po_number'         => $mat['po_number'] ?? null,
                    'po_issued'         => $mat['po_issued'] ?? null,
                    'submit_tpg'        => $mat['submit_tpg'] ?? null,
                    'approve_tpg'       => $mat['approve_tpg'] ?? null,
                    'manufacture'       => $mat['manufacture'] ?? null,
                    'fat'               => $mat['fat'] ?? null,
                    'delivery'          => $mat['delivery'] ?? null,
                    'material_on_site'  => $mat['material_on_site'] ?? null,
                ]);
            }
        }

        // 7. Simpan data Procurement Service
        if ($request->has('service')) {
            foreach ($request->service as $svc) {
                // Skip jika service_name kosong
                if (empty($svc['service_name'])) continue;

                $period->procurementServices()->create([
                    'period_id'     => $period->id,
                    'service_name'  => $svc['service_name'] ?? '',
                    'po_number'     => $svc['po_number'] ?? null,
                    'po_issued'     => $svc['po_issued'] ?? null,
                    'mobilization'  => $svc['mobilization'] ?? null,
                    'field_work'    => $svc['field_work'] ?? null,
                    'reporting'     => $svc['reporting'] ?? null,
                ]);
            }
        }

        // 8. Simpan data Construction
        if ($request->has('construction')) {
            foreach ($request->construction as $con) {
                // Skip jika description kosong (kecuali untuk total row)
                if (empty($con['description']) && !isset($con['is_total'])) continue;

                $period->constructions()->create([
                    'period_id'         => $period->id,
                    'description'       => $con['description'] ?? '',
                    'weight_factor'     => $con['weight_factor'] ?? 0,
                    'cum_last_plan'     => $con['cum_last_plan'] ?? 0,
                    'cum_last_actual'   => $con['cum_last_actual'] ?? 0,
                    'cum_last_variance' => $con['cum_last_variance'] ?? 0,
                    'this_plan'         => $con['this_plan'] ?? 0,
                    'this_actual'       => $con['this_actual'] ?? 0,
                    'cum_now_plan'      => $con['cum_now_plan'] ?? 0,
                    'cum_now_actual'    => $con['cum_now_actual'] ?? 0,
                    'cum_now_variance'  => $con['cum_now_variance'] ?? 0,
                    'status'            => $con['status'] ?? 'on schedule',
                    'is_total'          => isset($con['is_total']) ? true : false,
                ]);
            }
        }

        // 9. Simpan data Aktivitas (Activity)
        if ($request->has('activity')) {
            foreach ($request->activity as $scope => $acts) {
                foreach ($acts as $act) {
                    // Skip jika location kosong
                    if (empty($act['location'])) continue;

                    $period->activities()->create([
                        'period_id'             => $period->id,
                        'scope'                 => $scope,
                        'location'              => $act['location'] ?? '',
                        'achievement_this_week' => $act['achievement_this_week'] ?? '',
                        'progress'              => $act['progress'] ?? 0,
                        'plan_next_week'        => $act['plan_next_week'] ?? '',
                        'status'                => $act['status'] ?? 'ongoing',
                    ]);
                }
            }
        }

        // 10. Simpan data Issue
        if ($request->has('issue')) {
            foreach ($request->issue as $isu) {
                // Skip jika name kosong
                if (empty($isu['name'])) continue;

                $period->issues()->create([
                    'period_id'   => $period->id,
                    'name'        => $isu['name'] ?? '',
                    'location'    => $isu['location'] ?? '',
                    'equipment'   => $isu['equipment'] ?? '',
                    'description' => $isu['description'] ?? '',
                    'status'      => $isu['status'] ?? 'ongoing',
                ]);
            }
        }
    }

    // Index pages untuk akses langsung dari menu
    public function deskripsiIndex(Request $request)
    {
        $query = Project::query();
        $this->applySearch($query, $request);
        $projects = $query->orderBy('created_at', 'desc')->get();
        return view('pages.project.list', compact('projects'))->with('pageType', 'description');
    }

    public function progressIndex(Request $request)
    {
        $query = Project::query();
        $this->applySearch($query, $request);
        $projects = $query->orderBy('created_at', 'desc')->get();
        return view('pages.project.list', compact('projects'))->with('pageType', 'progress');
    }

    public function aktivitasIndex(Request $request)
    {
        $query = Project::query();
        $this->applySearch($query, $request);
        $projects = $query->orderBy('created_at', 'desc')->get();
        return view('pages.project.list', compact('projects'))->with('pageType', 'aktivitas');
    }

    public function isuIndex(Request $request)
    {
        $query = Project::query();
        $this->applySearch($query, $request);
        $projects = $query->orderBy('created_at', 'desc')->get();
        return view('pages.project.list', compact('projects'))->with('pageType', 'isu');
    }

    public function dokumentasiIndex(Request $request)
    {
        $query = Project::query();
        $this->applySearch($query, $request);
        $projects = $query->orderBy('created_at', 'desc')->get();
        return view('pages.project.list', compact('projects'))->with('pageType', 'dokumentasi');
    }

    // Show langsung ke description
    public function show(Project $project)
    {
        return redirect()->route('project.description', $project->id);
    }

    // Period handling untuk detail pages
    private function getCurrentPeriod(Project $project, Request $request)
    {
        $periods = $project->periods()->orderBy('start_date', 'desc')->get();

        // Cek jika ada period_id di query string
        if ($request->has('period_id')) {
            $selectedPeriod = $periods->where('id', $request->period_id)->first();
            if ($selectedPeriod) {
                return [$selectedPeriod, $periods];
            }
        }

        // Default ke periode pertama (terbaru)
        return [$periods->first(), $periods];
    }

    public function description(Project $project, Request $request)
    {
        // 1. Ambil SEMUA periode (Collection)
        $periods = $project->periods()
            ->with(['createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc') // Atau 'start_date' desc sesuai kebutuhan
            ->get();

        // 2. Logika Menentukan Current Period (SINGLE OBJECT)
        if ($request->has('period_id')) {
            // MENGGUNAKAN firstWhere: Mencari 1 item di dalam collection berdasarkan ID
            $currentPeriod = $periods->firstWhere('id', $request->period_id);
        } else {
            // Default: Ambil periode paling atas (terbaru)
            $currentPeriod = $periods->first();
        }

        // Safety Check: Jika period_id di URL salah/tidak ketemu, fallback ke yang pertama
        if (!$currentPeriod && $periods->isNotEmpty()) {
            $currentPeriod = $periods->first();
        }

        // 3. Load missing relation jika currentPeriod ada
        if ($currentPeriod) {
            // Pastikan ini Single Object sebelum di-load
            $currentPeriod->loadMissing(['createdBy', 'updatedBy']);
        }

        // 4. Ambil data progress
        // Pastikan logic getProgressChartData menghandle jika currentPeriod null (misal project baru tanpa periode)
        $progressData = $this->getProgressChartData($project, $currentPeriod);
        
        return view('pages.project.description', compact('project', 'currentPeriod', 'periods', 'progressData'));
    }

    // TAMBAHAN: Method untuk mengambil data grafik progress
    private function getProgressChartData($project, $currentPeriod)
    {
        if (!$currentPeriod) {
            return [
                'labels' => [],
                'planned' => [],
                'actual' => []
            ];
        }

        // Ambil semua data progress untuk periode yang dipilih, diurutkan berdasarkan waktu
        $progressRecords = ProjectProgress::where('period_id', $currentPeriod->id)
            ->where('period_id', $currentPeriod->id)
            ->orderBy('created_at')
            ->get();

        $labels = [];
        $planned = [];
        $actual = [];

        foreach ($progressRecords as $index => $record) {
            // Label berdasarkan urutan update (Update ke-1, ke-2, dst)
            $labels[] = 'Update ke-' . ($index + 1);
            
            // Data plan dan actual
            $planned[] = floatval($record->epc_total_cum_now_plan);
            $actual[] = floatval($record->epc_total_cum_now_actual);
        }

        // Jika tidak ada data, berikan data kosong
        if (empty($labels)) {
            $labels = ['Belum ada data'];
            $planned = [0];
            $actual = [0];
        }

        return [
            'labels' => $labels,
            'planned' => $planned,
            'actual' => $actual,
            'count' => count($progressRecords)
        ];
    }

    public function progress(Project $project, Request $request)
    {
        $project->load([
            'periods.epc',
            'periods.engineerings',
            'periods.constructions',
            'periods.procurementMaterials',
            'periods.procurementServices',
        ]);
        [$currentPeriod, $periods] = $this->getCurrentPeriod($project, $request);
        return view('pages.project.progress', compact('project', 'currentPeriod', 'periods'));
    }

    public function activities(Project $project, Request $request)
    {
        $project->load('periods.activities');
        [$currentPeriod, $periods] = $this->getCurrentPeriod($project, $request);
        return view('pages.project.activities', compact('project', 'currentPeriod', 'periods'));
    }

    public function issues(Project $project, Request $request)
    {
        $project->load('periods.issues');
        [$currentPeriod, $periods] = $this->getCurrentPeriod($project, $request);
        return view('pages.project.issues', compact('project', 'currentPeriod', 'periods'));
    }

    public function documentation(Project $project, Request $request)
    {
        $periods = $project->periods()->orderBy('start_date', 'desc')->get();
        $project->load([
            'periods.engineerings',
            'periods.procurementMaterials',
            'periods.procurementServices',
            'periods.documentations',
        ]);
        $selectedPeriodId = $request->query('period_id');
    
        if ($selectedPeriodId) {
            $currentPeriod = $periods->where('id', $selectedPeriodId)->first();
        } else {
            $currentPeriod = $periods->first(); // Atau logic default Anda
        }
        return view('pages.project.documentation.index', compact('project', 'currentPeriod', 'periods'));
    }

    public function edit(Request $request, Project $project)
    {
        $user = auth()->user();
    
        // Check basic permission
        if (!$user->isAdmin() && !$user->isUser()) {
            return redirect()->route('new_project.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit proyek.');
        }

        // Load all periods for this project
        $periods = $project->periods()->orderBy('start_date', 'desc')->get();

        // Get period from query string
        $periodId = $request->input('period_id');
        $period = null;

        if ($periodId) {
            $period = $periods->where('id', $periodId)->first();
        }

        // If no period found, get the first one
        if (!$period) {
            $period = $periods->first();
        }

        // Check if user can edit this specific period
        if ($period && !$period->canBeEditedBy($user)) {
            return redirect()->route('new_project.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit periode ini.');
        }

        // Load relasi-relasi ke period (pastikan relasi sudah didefinisikan di model Period)
        if ($period) {
            $period->load([
                'epc',
                'engineerings',
                'procurementMaterials',
                'procurementServices',
                'constructions',
                'activities',
                'issues',
            ]);
            $totalEpcRow = $period->epc->where('is_total', true)->first();
            $totalConRow = $period->constructions->where('is_total', true)->first();
        } else {
            // Jika tidak ada period, set null
            $totalEpcRow = null;
            $totalConRow = null;
        }

        $projects = Project::all();

        return view('pages.project.edit', compact('project', 'projects', 'periods', 'period', 'totalEpcRow', 'totalConRow'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'period_name'  => 'nullable|string|max:150',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'period_id'    => 'nullable|exists:periods,id',
            
            // TAMBAHAN: Validasi untuk progress input
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'weight' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'nullable|string',
        ]);

        $action = $request->input('period_action','save');

        // Update project
        $project->name = $validated['project_name'];
        $project->description = $validated['description'] ?? '';
        $project->save();

        // Get period
        $period = null;
        $routePeriodId = $request->route('period');
        if ($routePeriodId) {
            $period = $project->periods()->where('id', $routePeriodId)->first();
        }
        if (!$period && $request->filled('period_id')) {
            $period = $project->periods()->where('id', $request->period_id)->first();
        }
        if (!$period) {
            return back()->with('error', 'Periode yang diedit tidak ditemukan.')->withInput();
        }

        // Check permission
        if (!$period || !$period->canBeEditedBy(auth()->user())) {
            return back()->with('error', 'Akses ditolak atau periode tidak ditemukan.');
        }

        // Update period
        if ($action === 'submit') {
            $oldStatus = $period->status;
            if (auth()->user()->isUser()) {
                $period->status = 'submitted';
                $this->createTrackingEntry($period, $project, 'submit');
            }
        }

        if ($request->filled('period_name')) {
            $period->name = $request->input('period_name');
        }
        $period->start_date = $request->input('start_date', $period->start_date);
        $period->end_date = $request->input('end_date', $period->end_date);
        $period->updated_by = auth()->id();
        $period->save();

        // TAMBAHAN: Save/Update Project Progress
        $this->saveProjectProgress($period, $request);

        // Hapus dan re-insert data period (logic existing Anda)
        $period->epc()->delete();
        $period->engineerings()->delete();
        $period->procurementMaterials()->delete();
        $period->procurementServices()->delete();
        $period->constructions()->delete();
        $period->activities()->delete();
        $period->issues()->delete();

        $this->saveDetails($period, $request);
        
        // --- LOGIC BARU: CEK PROJECT COMPLETION ---
        // Panggil fungsi ini setiap kali update
        $this->checkProjectCompletion($project);

        return redirect()->route('new_project.index')->with('success','Perubahan disimpan (status periode: '.($period ? $period->status : '-').').');
    }

    public function destroy(Project $project)
    {
        // Hanya admin yang bisa delete
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('new_project.index')->with('error', 'Anda tidak memiliki akses untuk menghapus proyek.');
        }

        $project->delete();
        return redirect()->route('new_project.index')->with('success', 'Proyek berhasil dihapus.');
    }
    
    // TAMBAHAN: Method baru untuk save progress
    private function saveProjectProgress($period, $request)
    {
        $progressData = [
            'period_id' => $period->id,
            'progress_percentage' => $request->input('progress_percentage', 0),
            'weight' => $request->input('weight', 0),
            'notes' => $request->input('progress_notes'),
        ];
        
        // calculated_progress akan di-set otomatis via model boot
        
        // Update atau Create
        ProjectProgress::updateOrCreate(
            ['period_id' => $period->id],
            $progressData
        );
    }

    public function getProgressData(Period $period)
    {
        try {
            // Ambil data EPC untuk periode ini
            $epcs = $period->epc()->orderBy('created_at')->get();
            
            if ($epcs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No progress data found',
                    'data' => ['count' => 0]
                ]);
            }

            // Siapkan data untuk chart
            $labels = [];
            $planned = [];
            $actual = [];
            
            foreach ($epcs as $index => $epc) {
                // Hanya ambil data "Total" (is_total = 1)
                if ($epc->is_total == 1) {
                    $labels[] = 'Update ' . ($index + 1);
                    $planned[] = (float) $epc->cum_now_plan;
                    $actual[] = (float) $epc->cum_now_actual;
                }
            }

            // Jika tidak ada data total, ambil data terakhir dari setiap kategori
            if (empty($labels)) {
                $totalEpc = $epcs->where('description', 'Total')->first();
                if ($totalEpc) {
                    $labels[] = 'Current';
                    $planned[] = (float) $totalEpc->cum_now_plan;
                    $actual[] = (float) $totalEpc->cum_now_actual;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'count' => count($labels),
                    'labels' => $labels,
                    'planned' => $planned,
                    'actual' => $actual
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading progress data: ' . $e->getMessage(),
                'data' => ['count' => 0]
            ]);
        }
    }

    public function saveForm(Request $request)
    {
        $currentStep = $request->input('current_step');

        // If saving draft
        if ($currentStep === 'save') {
            // Save all form data as draft
            // Logic to save the tables: EPC, Engineering, Procurement, Construction, Activities
            return redirect()->route('new_project.index')->with('success', 'Data proyek berhasil disimpan!');
        }

        // If submitting for review
        if ($currentStep === 'submit') {
            // Save all form data and mark as submitted for review
            // Logic to save all the tables: EPC, Engineering, Procurement, Construction, Activities
            // and update status to submitted
            return redirect()->route('new_project.index')->with('success', 'Data proyek berhasil dikirim untuk review!');
        }

        // Handle other steps if needed
        return redirect()->route('new_project.index');
    }

    // Untuk dropdown proyek (AJAX)
    public function dropdownProjects()
    {
        $projects = Project::select('id', 'name')->get();
        return response()->json($projects);
    }

    // Untuk copy template (AJAX)
    public function copyTemplate($project_id)
    {
        $project = Project::with([
            'periods',
            'engineerings',
            'epc',
            'procurementMaterials',
            'procurementServices',
            'constructions',
            'activities',
            'issues',
        ])->findOrFail($project_id);

        return response()->json($project);
    }

    public function approve(Request $request, Project $project)
    {
        $periodId = $request->input('period_id');
        $period = $project->periods()->where('id', $periodId)->first();
        
        if (!$period) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan.');
        }

        $user = auth()->user()->isAdmin();
        
        // Check apakah user bisa approve periode ini
        if (!$user) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk approve periode ini.');
        }

        // Update Status Langsung ke Approved
        $period->status = 'approved';
        $period->updated_by = auth()->id();
        $period->save();

        $this->createTrackingEntry($period, $project, 'approve');
        
        // --- LOGIC BARU: CEK PROJECT COMPLETION ---
        // Panggil fungsi ini setiap kali update
        $this->checkProjectCompletion($project);

        return back()->with('success', 'Periode berhasil di-approve (Final).');
    }

    public function reject(Request $request, Project $project)
    {
        // Ambil period dari project (lebih aman)
        $periodId = $request->input('period_id');
        $period = $project->periods()->where('id', $periodId)->first();

        if (!$period) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan.');
        }

        // Check admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk reject periode ini.');
        }

        $oldStatus = $period->status;

        // Kembalikan ke saved
        $period->status = 'saved';
        $period->updated_by = auth()->id();
        $period->save();

        // Tracking (samakan dengan fungsi pertama)
        $this->createTrackingEntry($period, $project, 'reject', $oldStatus);

        return redirect()->back()->with('success', 'Periode dikembalikan ke User untuk revisi.');
    }

    private function createTrackingEntry(Period $period, Project $project, $action)
    {
        $subject = "{$project->name} - {$period->name}";
        $user = auth()->user();

        // Default Values
        $sender = $user->name;
        $recipient = 'Admin'; 
        $status = 'submission';

        if ($action === 'submit') {
            // User submit ke Admin
            $recipient = 'Admin Project';
        } 
        elseif ($action === 'approve') {
            // Admin approve
            $sender = 'Admin Project';
            $recipient = 'System'; // Final
            $status = 'approved';
        }
        elseif ($action === 'reject') {
            // Admin reject ke User
            $sender = 'Admin Project';
            $recipient = $period->createdBy->name ?? 'User';
            $status = 'rejected';
        }

        // Logic Create/Update Tracking yang jauh lebih sederhana
        // Kita catat history baru setiap ada action penting
        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => now(),
            'sender' => $sender,
            'recipient' => $recipient,
            'subject' => $subject,
            'status' => $status,
            'reviewer' => $action === 'submit' ? 'Admin' : 'User', // Penanda siapa yg harus action selanjutnya
        ]);
    }

    // Tambahkan method applySearch 
    private function applySearch($query, Request $request)
    {
        $user = auth()->user();
        
        // Batasi proyek untuk role user: hanya yang punya periode dibuat olehnya
        if ($user->isUser()) {
            // User hanya lihat project dimana dia membuat periodenya
            $query->whereHas('periods', function($q) use ($user){
                $q->where('created_by', $user->id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('name','LIKE',"%{$search}%")
                  ->orWhere('description','LIKE',"%{$search}%")
                  ->orWhereHas('periods', function($p) use ($search){
                      $p->where('name','LIKE',"%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->start_date;
            $end   = $request->end_date;
            $query->where(function($q) use ($start,$end){
                if ($start && $end) {
                    $q->whereBetween('created_at', [
                        \Carbon\Carbon::parse($start)->startOfDay(),
                        \Carbon\Carbon::parse($end)->endOfDay()
                    ]);
                } elseif ($start) {
                    $q->where('created_at','>=',\Carbon\Carbon::parse($start)->startOfDay());
                } elseif ($end) {
                    $q->where('created_at','<=',\Carbon\Carbon::parse($end)->endOfDay());
                }
            })->orWhereHas('periods', function($p) use ($start,$end){
                if ($start && $end) {
                    $p->where(function($w) use ($start,$end){
                        $w->whereBetween('start_date', [$start,$end])
                          ->orWhereBetween('end_date', [$start,$end]);
                    });
                } elseif ($start) {
                    $p->where('end_date','>=',$start);
                } elseif ($end) {
                    $p->where('start_date','<=',$end);
                }
            });
        }
        return $query;
    }

    // --- METHOD BARU: LOGIC PENYELESAIAN PROYEK ---
    private function checkProjectCompletion(Project $project)
    {
        // 1. Ambil semua periode milik project ini
        // Kita refresh relasi untuk memastikan data terbaru terambil
        $project->load('periods.projectProgress');
        $periods = $project->periods;

        if ($periods->isEmpty()) {
            return;
        }

        $totalWeight = 0;
        $totalAccumulatedProgress = 0;
        $allApproved = true;

        // 2. Loop cek setiap periode
        foreach($periods as $p) {
            // Syarat 1: Semua periode harus status 'approved'
            if ($p->status !== 'approved') {
                $allApproved = false;
            }

            $prog = $p->projectProgress; 
            if ($prog) {
                // Hitung total bobot (harus 1.0 di akhir)
                $totalWeight += floatval($prog->weight);
                
                // Hitung akumulasi (Progress * Weight)
                $totalAccumulatedProgress += (floatval($prog->progress_percentage) * floatval($prog->weight));
            }
        }

        // 3. Syarat Utama:
        // a. Semua periode Approved
        // b. Total Bobot >= 1.0 (Pake 0.999 toleransi float)
        // c. Total Progress >= 100% (Pake 99.99 toleransi float)
        if ($allApproved && $totalWeight >= 0.999 && $totalAccumulatedProgress >= 99.99) {
            
            // TANDAI SEMUA PERIODE SEBAGAI COMPLETED
            // Agar muncul di menu Completed/Arsip
            foreach($periods as $p) {
                // Cek biar gak update berulang-ulang kalau sudah completed
                if (!$p->is_completed) {
                    $p->update([
                        'is_completed' => true,
                        'completed_at' => now()
                    ]);
                }
            }
        }
    }

    // --- TAMBAHAN: INPUT PROYEK LAMPAU ---
    public function createPast()
    {
        // Hanya Admin yang boleh input proyek lampau
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('new_project.index')->with('error', 'Hanya Admin yang dapat menginput proyek lampau.');
        }

        $projects = Project::all();
        $periods = Period::all();
        
        // Return ke view baru
        return view('pages.completed-periods.create-past', compact('projects', 'periods'));
    }

    public function storePast(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('new_project.index')->with('error', 'Akses ditolak.');
        }

        // 1. Validasi (Mirip store biasa, tapi tambah field tanggal_selesai)
        $validated = $request->validate([
            'existing_project_id'   => 'nullable|exists:projects,id',
            'project_name'          => 'required|string|max:150',
            'description'           => 'nullable|string',
            'period_name'           => 'required|string|max:150',
            'start_date'            => 'nullable|date',
            'end_date'              => 'nullable|date', // Tidak perlu validasi after_or_equal ketat jika data lama
            
            // Input Khusus Proyek Lampau
            'past_completed_at'     => 'required|date', // Tanggal Approval/Selesai
            
            // Progress harus 100% dan Bobot 1 (Opsional: bisa dipaksa di code)
            'progress_percentage'   => 'required|numeric',
            'weight'                => 'required|numeric',
            'progress_notes'        => 'nullable|string',
        ]);

        $completionDate = Carbon::parse($request->past_completed_at);

        // 2. Handle Project
        if ($request->filled('existing_project_id')) {
            $project = Project::find($request->existing_project_id);
        } else {
            $project = Project::create([
                'name' => $request->project_name,
                'description' => $request->description,
                'created_at' => $completionDate, // Backdate project creation
                'updated_at' => $completionDate,
            ]);
        }

        // 3. Handle Period (LANGSUNG APPROVED & COMPLETED)
        $period = Period::create([
            'project_id'    => $project->id,
            'name'          => $request->period_name,
            'start_date'    => $request->start_date ?? $completionDate->copy()->subMonth(), // Default 1 bulan sblm selesai jika kosong
            'end_date'      => $request->end_date ?? $completionDate,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
            
            // Status Langsung Final
            'status'        => 'approved', 
            'is_completed'  => true,
            'completed_at'  => $completionDate, // Sesuai inputan user
            
            // Backdate timestamp record
            'created_at'    => $completionDate,
            'updated_at'    => $completionDate,
        ]);

        // 4. Update project desc jika baru
        if ($request->filled('description') && !$request->filled('existing_project_id')) {
            $project->description = $request->input('description');
            $project->save();
        }

        // 5. Save Details (EPC, Engineering, dll)
        // Kita gunakan method saveDetails yang sudah ada
        $this->saveDetails($period, $request);
        
        // 6. Save Project Progress (Paksa 100% jika user lupa, atau ikut inputan)
        // Biasanya proyek lampau dianggap 100%
        ProjectProgress::create([
            'period_id' => $period->id,
            'progress_percentage' => 100, // Force 100%
            'weight' => 1,                // Force Bobot 1
            'notes' => $request->input('progress_notes', 'Input data historis proyek lampau'),
            'created_at' => $completionDate,
            'updated_at' => $completionDate,
        ]);

        // 7. Create Fake Tracking History (Agar tercatat pernah diapprove)
        // Create Submission Log
        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => $completionDate->copy()->subHours(2), // Mundurin dikit
            'sender' => auth()->user()->name . ' (Admin - Migrasi Data)',
            'recipient' => 'System',
            'subject' => "{$project->name} - {$period->name}",
            'status' => 'submission',
            'reviewer' => 'System',
            'created_at' => $completionDate->copy()->subHours(2),
        ]);

        // Create Approval Log
        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => $completionDate,
            'sender' => 'System',
            'recipient' => 'System',
            'subject' => "{$project->name} - {$period->name}",
            'status' => 'approved',
            'reviewer' => 'System',
            'created_at' => $completionDate,
        ]);

        $projectId = $project->id;
        $action = $request->input('save_action', 'finish'); // Default finish

        if ($action === 'next') {
            // FLOW 1: Simpan & Lanjut Input Periode Lain (Redirect Back)
            return redirect()->route('project.create-past', ['project_id' => $project->id])
                ->with('success', "Periode '{$period->name}' disimpan! Silakan input periode berikutnya untuk proyek ini.");
        } else {
            // FLOW 2: Simpan & Selesai (Redirect ke Index)
            return redirect()->route('completed-periods.index')
                ->with('success', "Proyek Lampau '{$project->name}' berhasil diarsipkan lengkap.");
        }
    }
}
