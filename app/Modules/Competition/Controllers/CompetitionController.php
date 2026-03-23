<?php

namespace App\Modules\Competition\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Competition\Models\Competition;
use App\Modules\Competition\Models\CompetitionEntry;
use App\Modules\Competition\Services\CompetitionService;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index()
    {
        return Competition::with('category')->where('status', 'active')->get();
    }

    public function enter(Request $request, Competition $competition, CompetitionService $service)
    {
        $validated = $request->validate([
            'media_type' => 'required|in:image,video',
            'media_url'  => 'required|string',
            'caption'    => 'nullable|string',
        ]);

        return $service->enterCompetition($competition, $request->user(), $validated);
    }

    public function vote(CompetitionEntry $entry, Request $request, CompetitionService $service)
    {
        return $service->vote($entry, $request->user());
    }

    public function results(Competition $competition)
    {
        return $competition->entries()->orderByDesc('votes_count')->get();
    }
}
