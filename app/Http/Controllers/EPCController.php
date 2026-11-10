<?php

namespace App\Http\Controllers;

use App\Models\Epc;
use App\Models\Period;
use Illuminate\Http\Request;

class EPCController extends Controller
{
    public function store(Request $request, Period $period)
    {
        $request->validate([
            'epc.*.description' => 'required|string|max:255',
            'epc.*.weight_factor' => 'required|numeric',
            'epc.*.cum_last_plan' => 'required|numeric',
            'epc.*.cum_last_actual' => 'required|numeric',
            'epc.*.cum_last_variance' => 'required|numeric',
            'epc.*.this_plan' => 'required|numeric',
            'epc.*.this_actual' => 'required|numeric',
            'epc.*.cum_now_plan' => 'required|numeric',
            'epc.*.cum_now_actual' => 'required|numeric',
            'epc.*.cum_now_variance' => 'required|numeric',
            'epc.*.status' => 'required|in:ongoing,done,delayed',
        ]);

        foreach ($request->epc as $epcData) {
            Epc::create([
                'period_id' => $period->id,
                'description' => $epcData['description'],
                'weight_factor' => $epcData['weight_factor'],
                'cum_last_plan' => $epcData['cum_last_plan'],
                'cum_last_actual' => $epcData['cum_last_actual'],
                'cum_last_variance' => $epcData['cum_last_variance'],
                'this_plan' => $epcData['this_plan'],
                'this_actual' => $epcData['this_actual'],
                'cum_now_plan' => $epcData['cum_now_plan'],
                'cum_now_actual' => $epcData['cum_now_actual'],
                'cum_now_variance' => $epcData['cum_now_variance'],
                'status' => $epcData['status'],
            ]);
        }

        return redirect()->back()->with('success', 'Data EPC berhasil disimpan!');
    }

    public function update(Request $request, Period $period)
    {
        // Delete existing records for this period
        Epc::where('period_id', $period->id)->delete();
        
        // Create new records
        return $this->store($request, $period);
    }
}
