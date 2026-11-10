<?php

namespace App\Http\Controllers;

use App\Models\Construction;
use App\Models\Period;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    public function store(Request $request, Period $period)
    {
        $request->validate([
            'construction.*.column_a' => 'nullable|string',
            'construction.*.column_b' => 'nullable|string',
            'construction.*.column_c' => 'nullable|string',
            'construction.*.column_d' => 'nullable|string',
        ]);

        $counter = 1;
        foreach ($request->construction as $constructionData) {
            Construction::create([
                'period_id' => $period->id,
                'no' => $counter++,
                'column_a' => $constructionData['column_a'],
                'column_b' => $constructionData['column_b'],
                'column_c' => $constructionData['column_c'],
                'column_d' => $constructionData['column_d'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Construction berhasil disimpan!');
    }

    public function update(Request $request, Period $period)
    {
        // Delete existing records for this period
        Construction::where('period_id', $period->id)->delete();
        
        // Create new records
        return $this->store($request, $period);
    }
}
