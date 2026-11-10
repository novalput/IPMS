<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tracking;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Tracking::with('period.project');

        // Filter tanggal
        if ($request->start_date && $request->end_date) {
            $query->where(function($q) use ($request) {
                // Mencari di issued_date ATAU updated_at yang berada dalam range tanggal
                $q->whereBetween('issued_date', [$request->start_date, $request->end_date])
                  ->orWhere(function($subQ) use ($request) {
                      $subQ->whereDate('updated_at', '>=', $request->start_date)
                           ->whereDate('updated_at', '<=', $request->end_date);
                  });
            });
        }

        // Filter hal
        if ($request->subject) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        // Filter kata kunci
        if ($request->keyword) {
            $query->where(function($q) use ($request) {
                $q->where('sender', 'like', '%' . $request->keyword . '%')
                  ->orWhere('recipient', 'like', '%' . $request->keyword . '%')
                  ->orWhere('subject', 'like', '%' . $request->keyword . '%')
                  ->orWhere('reviewer', 'like', '%' . $request->keyword . '%');
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $trackings = $query->orderBy('period_id', 'asc')
                          ->orderBy('created_at', 'asc')
                          ->paginate(5)->withQueryString();
        
        $allSubjects = Tracking::select('subject')->distinct()->orderBy('subject')->pluck('subject');

        return view('pages.general.tracking.index', compact('trackings', 'allSubjects'));
    }
}
