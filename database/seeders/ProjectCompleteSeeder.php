<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Period;
use App\Models\User;
use App\Models\Activity;
use App\Models\Issue;
use App\Models\Documentation;
use App\Models\Epc;
use App\Models\Engineering;
use App\Models\Construction;
use App\Models\ProjectProgress;
use App\Models\Tracking;
use App\Models\ProcurementMaterial;
use App\Models\ProcurementService;
use Carbon\Carbon;

class ProjectCompleteSeeder extends Seeder
{
    public function run()
    {
        // 1. CREATE USERS
        $admin = User::create([
            'username' => 'admin',
            'name' => 'IPMS Administrator',
            'email' => 'admin@ipms.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
            'approved_at' => now(),
            'job_position' => 'System Administrator',
            'avatar_url' => 'images/avatar-default.svg',
        ]);
        
        $projectManager = User::create([
            'username' => 'project.manager',
            'name' => 'Project Manager',
            'email' => 'project.manager@ipms.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'job_position' => 'Project Manager',
            'avatar_url' => 'images/avatar-default.svg',
        ]);

        $coordinator = User::create([
            'username' => 'project.coordinator',
            'name' => 'Project Coordinator',
            'email' => 'project.coordinator@ipms.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'job_position' => 'Project Coordinator',
        ]);

        User::create([
            'username' => 'pending.user',
            'name' => 'Pending User',
            'email' => 'pending.user@ipms.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'pending',
            'job_position' => 'Project Contributor',
        ]);

        // 2. CREATE PROJECT
        $project = Project::create([
            'name' => 'Proyek Sample - Demo Project',
            'description' => 'Project demo untuk testing sistem',
        ]);

        // 3. CREATE PERIOD
        $period = Period::create([
            'project_id' => $project->id,
            'name' => 'Period 1',
            'week' => null,
            'month' => null,
            'start_date' => '2025-08-01',
            'end_date' => '2025-09-30',
            'created_by' => $coordinator->id,
            'updated_by' => $projectManager->id,
            'status' => 'submitted',
        ]);

        // 4. CREATE ACTIVITIES
        Activity::create([
            'period_id' => $period->id,
            'scope' => 'A',
            'location' => 'Site Area 1',
            'achievement_this_week' => 'Completed foundation work',
            'progress' => 25.50,
            'plan_next_week' => 'Start steel structure installation',
            'status' => 'ongoing',
        ]);

        // 5. CREATE ISSUES
        Issue::create([
            'period_id' => $period->id,
            'name' => 'Material Delay',
            'location' => 'Warehouse',
            'equipment' => 'Steel Beam',
            'description' => 'Steel beam delivery delayed due to weather conditions',
            'status' => 'ongoing',
        ]);

        // 6. CREATE DOCUMENTATION
        Documentation::create([
            'period_id' => $period->id,
            'file_path' => 'images/documentation/sample_doc.jpg',
            'title' => 'Progress Documentation Week 1',
            'description' => 'Sample progress photos and project report.',
        ]);

        // 7. CREATE EPC DATA
        $epcData = [
            ['description' => 'Engineering', 'weight_factor' => 5.00, 'cum_last_plan' => 5.00, 'cum_last_actual' => 5.00, 'cum_now_plan' => 5.00, 'cum_now_actual' => 5.00, 'status' => 'on schedule', 'is_total' => 0],
            ['description' => 'Procurement', 'weight_factor' => 65.00, 'cum_last_plan' => 65.00, 'cum_last_actual' => 65.00, 'cum_now_plan' => 65.00, 'cum_now_actual' => 65.00, 'status' => 'on schedule', 'is_total' => 0],
            ['description' => 'Construction', 'weight_factor' => 27.00, 'cum_last_plan' => 27.00, 'cum_last_actual' => 26.99, 'cum_now_plan' => 27.00, 'cum_now_actual' => 26.99, 'status' => 'delayed', 'is_total' => 0],
            ['description' => 'Commissioning', 'weight_factor' => 3.00, 'cum_last_plan' => 3.00, 'cum_last_actual' => 2.97, 'cum_now_plan' => 3.00, 'cum_now_actual' => 2.97, 'status' => 'delayed', 'is_total' => 0],
            ['description' => 'Total', 'weight_factor' => 100.00, 'cum_last_plan' => 100.00, 'cum_last_actual' => 99.96, 'cum_now_plan' => 100.00, 'cum_now_actual' => 99.96, 'status' => 'delayed', 'is_total' => 1],
        ];

        foreach ($epcData as $epc) {
            Epc::create([
                'period_id' => $period->id,
                'description' => $epc['description'],
                'weight_factor' => $epc['weight_factor'],
                'cum_last_plan' => $epc['cum_last_plan'],
                'cum_last_actual' => $epc['cum_last_actual'],
                'cum_last_variance' => $epc['cum_last_actual'] - $epc['cum_last_plan'],
                'this_plan' => 0.00,
                'this_actual' => 0.00,
                'cum_now_plan' => $epc['cum_now_plan'],
                'cum_now_actual' => $epc['cum_now_actual'],
                'cum_now_variance' => $epc['cum_now_actual'] - $epc['cum_now_plan'],
                'status' => $epc['status'],
                'is_total' => $epc['is_total'],
            ]);
        }

        // 8. CREATE CONSTRUCTION DATA
        $constructionData = [
            ['description' => 'Engineering', 'weight_factor' => 5.00, 'cum_last_plan' => 5.00, 'cum_last_actual' => 5.00, 'cum_now_plan' => 5.00, 'cum_now_actual' => 5.00, 'status' => 'on schedule', 'is_total' => 0],
            ['description' => 'Procurement', 'weight_factor' => 65.00, 'cum_last_plan' => 65.00, 'cum_last_actual' => 65.00, 'cum_now_plan' => 65.00, 'cum_now_actual' => 65.00, 'status' => 'on schedule', 'is_total' => 0],
            ['description' => 'Construction', 'weight_factor' => 27.00, 'cum_last_plan' => 27.00, 'cum_last_actual' => 26.99, 'cum_now_plan' => 27.00, 'cum_now_actual' => 26.99, 'status' => 'delayed', 'is_total' => 0],
            ['description' => 'Commissioning', 'weight_factor' => 3.00, 'cum_last_plan' => 3.00, 'cum_last_actual' => 2.97, 'cum_now_plan' => 3.00, 'cum_now_actual' => 2.97, 'status' => 'delayed', 'is_total' => 0],
            ['description' => 'Total', 'weight_factor' => 100.00, 'cum_last_plan' => 100.00, 'cum_last_actual' => 99.96, 'cum_now_plan' => 100.00, 'cum_now_actual' => 99.96, 'status' => 'delayed', 'is_total' => 1],
        ];

        foreach ($constructionData as $construction) {
            Construction::create([
                'period_id' => $period->id,
                'description' => $construction['description'],
                'weight_factor' => $construction['weight_factor'],
                'cum_last_plan' => $construction['cum_last_plan'],
                'cum_last_actual' => $construction['cum_last_actual'],
                'cum_last_variance' => $construction['cum_last_actual'] - $construction['cum_last_plan'],
                'this_plan' => 0.00,
                'this_actual' => 0.00,
                'cum_now_plan' => $construction['cum_now_plan'],
                'cum_now_actual' => $construction['cum_now_actual'],
                'cum_now_variance' => $construction['cum_now_actual'] - $construction['cum_now_plan'],
                'status' => $construction['status'],
                'is_total' => $construction['is_total'],
            ]);
        }

        // 9. CREATE ENGINEERING DATA
        $engineeringData = [
            ['kategori' => 'Drawing', 'subkategori' => 'Structural', 'sub' => 'A.1', 'doc_part1' => 'DRW', 'doc_part2' => 'STR', 'doc_part3' => '001', 'doc_part4' => 'REV', 'doc_part5' => 'A', 'doc_part6' => '2025', 'document_name' => 'Foundation Drawing', 'approval_status' => 'ongoing'],
            ['kategori' => 'Drawing', 'subkategori' => 'Structural', 'sub' => 'A.2', 'doc_part1' => 'DRW', 'doc_part2' => 'STR', 'doc_part3' => '002', 'doc_part4' => 'REV', 'doc_part5' => 'B', 'doc_part6' => '2025', 'document_name' => 'Steel Structure Drawing', 'approval_status' => 'done'],
            ['kategori' => 'Drawing', 'subkategori' => 'Electrical', 'sub' => 'B.1', 'doc_part1' => 'DRW', 'doc_part2' => 'ELC', 'doc_part3' => '001', 'doc_part4' => 'REV', 'doc_part5' => 'A', 'doc_part6' => '2025', 'document_name' => 'Power Distribution Drawing', 'approval_status' => 'ongoing'],
        ];

        foreach ($engineeringData as $eng) {
            Engineering::create([
                'period_id' => $period->id,
                'kategori' => $eng['kategori'],
                'subkategori' => $eng['subkategori'],
                'sub' => $eng['sub'],
                'doc_part1' => $eng['doc_part1'],
                'doc_part2' => $eng['doc_part2'],
                'doc_part3' => $eng['doc_part3'],
                'doc_part4' => $eng['doc_part4'],
                'doc_part5' => $eng['doc_part5'],
                'doc_part6' => $eng['doc_part6'],
                'document_name' => $eng['document_name'],
                'approval_status' => $eng['approval_status'],
            ]);
        }

        // 10. CREATE PROJECT PROGRESS
        ProjectProgress::create([
            'period_id' => $period->id,
            'progress_percentage' => 99.96,
            'weight' => 1.00,
            'notes' => 'Sample progress snapshot for the seeded project.',
        ]);

        // 11. CREATE TRACKING DATA
        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => '2025-09-15',
            'sender' => 'Kontraktor',
            'recipient' => 'Team Leader Contractor',
            'subject' => $project->name . ' - ' . $period->name,
            'status' => 'approved', // Reviewer 1 sudah approve
            'reviewer' => 'Team Leader Contractor',
        ]);

        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => '2025-09-15',
            'sender' => 'Team Leader Contractor',
            'recipient' => 'Project Manager Contractor',
            'subject' => $project->name . ' - ' . $period->name,
            'status' => 'submission', 
            'reviewer' => 'Project Manager Contractor',
        ]);

        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => '2025-09-15',
            'sender' => 'Project Manager Contractor',
            'recipient' => 'Team Leader IPMS',
            'subject' => $project->name . ' - ' . $period->name,
            'status' => 'submission', 
            'reviewer' => 'Team Leader IPMS',
        ]);

        Tracking::create([
            'period_id' => $period->id,
            'issued_date' => '2025-09-15',
            'sender' => 'Team Leader IPMS',
            'recipient' => 'Project Manager IPMS',
            'subject' => $project->name . ' - ' . $period->name,
            'status' => 'submission', 
            'reviewer' => 'Project Manager IPMS',
        ]);

        // 11. CREATE PROCUREMENT Material DATA
        $procurementMaterialData = [
            [
                'material_name' => 'Steel Structure - Main Frame',
                'po_number' => 'PO-MAT-001/2025',
                'po_issued' => '2025-08-05',
                'submit_tpg' => '2025-08-10',
                'approve_tpg' => '2025-08-15',
                'manufacture' => '2025-08-20',
                'fat' => '2025-09-01',
                'delivery' => '2025-09-10',
                'material_on_site' => '2025-09-15',
            ],
            [
                'material_name' => 'Electrical Panel - Control System',
                'po_number' => 'PO-MAT-002/2025',
                'po_issued' => '2025-08-07',
                'submit_tpg' => '2025-08-12',
                'approve_tpg' => '2025-08-17',
                'manufacture' => '2025-08-25',
                'fat' => '2025-09-05',
                'delivery' => '2025-09-12',
                'material_on_site' => '2025-09-17',
            ],
            [
                'material_name' => 'Foundation Materials - Concrete & Rebar',
                'po_number' => 'PO-MAT-003/2025',
                'po_issued' => '2025-08-03',
                'submit_tpg' => '2025-08-08',
                'approve_tpg' => '2025-08-13',
                'manufacture' => '2025-08-18',
                'fat' => '2025-08-28',
                'delivery' => '2025-09-02',
                'material_on_site' => '2025-09-08',
            ],
            [
                'material_name' => 'Cable & Wiring System',
                'po_number' => 'PO-MAT-004/2025',
                'po_issued' => '2025-08-12',
                'submit_tpg' => '2025-08-17',
                'approve_tpg' => '2025-08-22',
                'manufacture' => '2025-09-01',
                'fat' => '2025-09-15',
                'delivery' => '2025-09-20',
                'material_on_site' => null, // Belum sampai di site
            ],
            [
                'material_name' => 'Protective Equipment & Safety Tools',
                'po_number' => 'PO-MAT-005/2025',
                'po_issued' => '2025-08-15',
                'submit_tpg' => '2025-08-20',
                'approve_tpg' => '2025-08-25',
                'manufacture' => '2025-09-03',
                'fat' => null, // Belum selesai FAT
                'delivery' => null,
                'material_on_site' => null,
            ],
        ];

        foreach ($procurementMaterialData as $material) {
            ProcurementMaterial::create([
                'period_id' => $period->id,
                'material_name' => $material['material_name'],
                'po_number' => $material['po_number'],
                'po_issued' => $material['po_issued'],
                'submit_tpg' => $material['submit_tpg'],
                'approve_tpg' => $material['approve_tpg'],
                'manufacture' => $material['manufacture'],
                'fat' => $material['fat'],
                'delivery' => $material['delivery'],
                'material_on_site' => $material['material_on_site'],
            ]);
        }
        // 12. CREATE PROCUREMENT Service DATA
        $procurementServiceData = [
            [
                'service_name' => 'Site Survey & Soil Investigation',
                'po_number' => 'PO-SRV-001/2025',
                'po_issued' => '2025-08-01',
                'mobilization' => '2025-08-03',
                'field_work' => '2025-08-05',
                'reporting' => '2025-08-15',
            ],
            [
                'service_name' => 'Construction Management Services',
                'po_number' => 'PO-SRV-002/2025',
                'po_issued' => '2025-08-04',
                'mobilization' => '2025-08-06',
                'field_work' => '2025-08-10',
                'reporting' => '2025-09-10',
            ],
            [
                'service_name' => 'Quality Control & Testing Services',
                'po_number' => 'PO-SRV-003/2025',
                'po_issued' => '2025-08-10',
                'mobilization' => '2025-08-12',
                'field_work' => '2025-08-15',
                'reporting' => '2025-09-15',
            ],
            [
                'service_name' => 'Equipment Installation & Commissioning',
                'po_number' => 'PO-SRV-004/2025',
                'po_issued' => '2025-08-20',
                'mobilization' => '2025-08-25',
                'field_work' => '2025-09-01',
                'reporting' => null, // Belum selesai reporting
            ],
            [
                'service_name' => 'Safety Training & Consultation',
                'po_number' => 'PO-SRV-005/2025',
                'po_issued' => '2025-08-18',
                'mobilization' => '2025-08-22',
                'field_work' => null, // Belum mulai field work
                'reporting' => null,
            ],
            [
                'service_name' => 'Environmental Impact Assessment',
                'po_number' => 'PO-SRV-006/2025',
                'po_issued' => '2025-08-08',
                'mobilization' => '2025-08-10',
                'field_work' => '2025-08-14',
                'reporting' => '2025-08-25',
            ],
        ];

        foreach ($procurementServiceData as $service) {
            ProcurementService::create([
                'period_id' => $period->id,
                'service_name' => $service['service_name'],
                'po_number' => $service['po_number'],
                'po_issued' => $service['po_issued'],
                'mobilization' => $service['mobilization'],
                'field_work' => $service['field_work'],
                'reporting' => $service['reporting'],
            ]);
        }
    }
}