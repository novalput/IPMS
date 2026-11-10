<?php

namespace App\Http\Controllers;

use App\Models\Engineering;
use App\Models\Period;
use Illuminate\Http\Request;

class EngineeringController extends Controller
{
    public function store(Request $request, Period $period)
    {
        $request->validate([
            'engineering.*.doc_part1' => 'required|string|max:50',
            'engineering.*.doc_part2' => 'required|string|max:50',
            'engineering.*.doc_part3' => 'required|string|max:50',
            'engineering.*.doc_part4' => 'required|string|max:50',
            'engineering.*.doc_part5' => 'required|string|max:50',
            'engineering.*.doc_part6' => 'required|string|max:50',
            'engineering.*.document_name' => 'required|string|max:255',
            'engineering.*.approval_status' => 'required|in:A,B,C,D',
        ]);

        $counter = 1;
        foreach ($request->engineering as $engineeringData) {
            Engineering::create([
                'period_id' => $period->id,
                'no' => $counter++,
                'doc_part1' => $engineeringData['doc_part1'],
                'doc_part2' => $engineeringData['doc_part2'],
                'doc_part3' => $engineeringData['doc_part3'],
                'doc_part4' => $engineeringData['doc_part4'],
                'doc_part5' => $engineeringData['doc_part5'],
                'doc_part6' => $engineeringData['doc_part6'],
                'document_name' => $engineeringData['document_name'],
                'approval_status' => $engineeringData['approval_status'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Engineering berhasil disimpan!');
    }

    public function update(Request $request, Period $period)
    {
        // Delete existing records for this period
        Engineering::where('period_id', $period->id)->delete();
        
        // Create new records
        return $this->store($request, $period);
    }
}
