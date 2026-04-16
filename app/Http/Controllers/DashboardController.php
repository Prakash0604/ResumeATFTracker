<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * Shows the user's main dashboard with:
 * - Summary stats (total uploads, avg score, best score)
 * - Recent resume list with scores
 * - Score trend over time (for chart)
 * - Quick tips based on latest analysis
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Aggregate stats for dashboard cards
        $stats = [
            'total_resumes'  => $user->resumes()->analyzed()->count(),
            'avg_score'      => (int) $user->resumes()->analyzed()
                ->join('analyses', 'resumes.id', '=', 'analyses.resume_id')
                ->avg('overall_score'),
            'best_score'     => (int) $user->resumes()->analyzed()
                ->join('analyses', 'resumes.id', '=', 'analyses.resume_id')
                ->max('overall_score'),
            'pending_count'  => $user->resumes()->pending()->count(),
        ];

        // Latest 5 resumes with analysis
        $recentResumes = $user->resumes()
            ->with('analysis')
            ->latest()
            ->limit(5)
            ->get();

        // Score trend data for Chart.js
        $scoreTrend = $user->resumes()
            ->analyzed()
            ->with('analysis')
            ->latest()
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn ($r) => [
                'date'  => $r->created_at->format('M d'),
                'score' => $r->analysis->overall_score ?? 0,
                'label' => $r->original_filename,
            ])
            ->values();

        // Most common missing keywords across all resumes
        $topMissingKeywords = DB::table('analyses')
            ->join('resumes', 'resumes.id', '=', 'analyses.resume_id')
            ->where('resumes.user_id', $user->id)
            ->whereNotNull('analyses.missing_keywords')
            ->pluck('analyses.missing_keywords')
            ->flatMap(fn ($json) => json_decode($json, true) ?? [])
            ->countBy()
            ->sortDesc()
            ->take(8)
            ->keys();
        $topMissingKeywords = collect([]);

        return view('dashboard.index', compact('stats', 'recentResumes', 'scoreTrend', 'topMissingKeywords'));
    }
}
