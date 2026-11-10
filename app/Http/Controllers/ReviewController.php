<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function show(Period $period)
    {
        // Load the project and related data for review
        $project = $period->project;
        
        // Check if user has reviewer role
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk mereview.');
        }
        
        return view('pages.project.review', compact('project', 'period'));
    }
    
    public function submit(Request $request, Period $period)
    {
        $request->validate([
            'review_comments' => 'nullable|string',
            'action' => 'required|in:approve,reject',
        ]);
        
        $action = $request->action;
        $comments = $request->review_comments;
        
        // Update period status based on review action
        if ($action == 'approve') {
            $period->update([
                'status' => 'approved',
                'review_comments' => $comments,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            
            return redirect()->route('dashboard')->with('success', 'Proyek telah disetujui.');
        } else {
            $period->update([
                'status' => 'rejected',
                'review_comments' => $comments,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            
            return redirect()->route('dashboard')->with('success', 'Proyek telah ditolak dan dikembalikan untuk revisi.');
        }
    }
}
