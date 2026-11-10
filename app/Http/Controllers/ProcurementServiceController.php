<?php

namespace App\Http\Controllers;

use App\Models\ProcurementService;
use App\Models\Period;
use Illuminate\Http\Request;

class ProcurementServiceController extends Controller
{
    public function store(Request $request, Period $period)
    {
        $request->validate([
            'service.*.service_name' => 'required|string|max:255',
            'service.*.po_number' => 'nullable|string|max:255',
            'service.*.po_issued' => 'nullable|date',
            'service.*.mobilization' => 'nullable|date',
            'service.*.field_work' => 'nullable|date',
            'service.*.reporting' => 'nullable|date',
        ]);

        foreach ($request->service as $serviceData) {
            ProcurementService::create([
                'period_id' => $period->id,
                'service_name' => $serviceData['service_name'],
                'po_number' => $serviceData['po_number'],
                'po_issued' => $serviceData['po_issued'],
                'mobilization' => $serviceData['mobilization'],
                'field_work' => $serviceData['field_work'],
                'reporting' => $serviceData['reporting'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Procurement Service berhasil disimpan!');
    }

    public function update(Request $request, Period $period)
    {
        // Delete existing records for this period
        ProcurementService::where('period_id', $period->id)->delete();
        
        // Create new records
        return $this->store($request, $period);
    }
}
