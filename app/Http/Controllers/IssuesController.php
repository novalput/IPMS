<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Period;

class IssuesController extends Controller
{
    // Simpan data isu (AJAX atau form)
    public function store(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:periods,id',
            'issue.*.name' => 'required|string|max:255',
            'issue.*.location' => 'nullable|string|max:255',
            'issue.*.equipment' => 'nullable|string|max:255',
            'issue.*.description' => 'nullable|string',
            'issue.*.status' => 'required|string|in:ongoing,done',
        ]);

        $period = Period::findOrFail($request->period_id);

        foreach ($request->issue as $isu) {
            $period->issues()->create([
                'name'        => $isu['name'],
                'location'    => $isu['location'] ?? '',
                'equipment'   => $isu['equipment'] ?? '',
                'description' => $isu['description'] ?? '',
                'status'      => $isu['status'],
            ]);
        }

        return redirect()->back()->with('success', 'Data isu berhasil disimpan!');
    }
}