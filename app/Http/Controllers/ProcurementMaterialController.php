<?php

namespace App\Http\Controllers;

use App\Models\ProcurementMaterial;
use App\Models\Period;
use Illuminate\Http\Request;

class ProcurementMaterialController extends Controller
{
    public function store(Request $request, Period $period)
    {
        $request->validate([
            'material.*.material_name' => 'required|string|max:255',
            'material.*.po_number' => 'nullable|string|max:255',
            'material.*.po_issued' => 'nullable|date',
            'material.*.submit_tpg' => 'nullable|date',
            'material.*.approve_tpg' => 'nullable|date',
            'material.*.manufacture' => 'nullable|date',
            'material.*.fat' => 'nullable|date',
            'material.*.delivery' => 'nullable|date',
            'material.*.material_on_site' => 'nullable|date',
        ]);

        foreach ($request->material as $materialData) {
            ProcurementMaterial::create([
                'period_id' => $period->id,
                'material_name' => $materialData['material_name'],
                'po_number' => $materialData['po_number'],
                'po_issued' => $materialData['po_issued'],
                'submit_tpg' => $materialData['submit_tpg'],
                'approve_tpg' => $materialData['approve_tpg'],
                'manufacture' => $materialData['manufacture'],
                'fat' => $materialData['fat'],
                'delivery' => $materialData['delivery'],
                'material_on_site' => $materialData['material_on_site'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Procurement Material berhasil disimpan!');
    }

    public function update(Request $request, Period $period)
    {
        // Delete existing records for this period
        ProcurementMaterial::where('period_id', $period->id)->delete();
        
        // Create new records
        return $this->store($request, $period);
    }
}
