<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Period;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request, Period $period)
    {
        $request->validate([
            'activity.*.location' => 'required|string|max:255',
            'activity.*.achievement_this_week' => 'required|string',
            'activity.*.progress' => 'required|numeric|between:0,100',
            'activity.*.plan_next_week' => 'required|string',
            'activity.*.status' => 'required|in:ongoing,done,delayed',
        ]);

        foreach ($request->activity as $activityData) {
            Activity::create([
                'period_id' => $period->id,
                'scope' => 'Scope A', // Default scope, can be modified as needed
                'location' => $activityData['location'],
                'achievement_this_week' => $activityData['achievement_this_week'],
                'progress' => $activityData['progress'],
                'plan_next_week' => $activityData['plan_next_week'],
                'status' => $activityData['status'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Activity berhasil disimpan!');
    }

    public function update(Request $request, Period $period)
    {
        // Delete existing records for this period
        Activity::where('period_id', $period->id)->delete();
        
        // Create new records
        return $this->store($request, $period);
    }
}
