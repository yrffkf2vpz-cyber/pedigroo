<?php

namespace App\Modules\Competition\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Competition\Models\Competition;
use App\Modules\Competition\Models\CompetitionCategory;
use App\Modules\Competition\Services\CompetitionService;
use Illuminate\Http\Request;

class CompetitionAdminController extends Controller
{
    public function index()
    {
        return Competition::with('category')
            ->orderByDesc('starts_at')
            ->get();
    }

    public function store(Request $request, CompetitionService $service)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:competition_categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at'   => 'required|date',
            'ends_at'     => 'required|date|after:starts_at',
        ]);

        $validated['is_auto_generated'] = false;

        return $service->createCompetition($validated);
    }

    public function update(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'starts_at'   => 'sometimes|date',
            'ends_at'     => 'sometimes|date|after:starts_at',
            'status'      => 'sometimes|in:upcoming,active,finished',
        ]);

        $competition->update($validated);

        return $competition;
    }

    public function finish(Competition $competition, CompetitionService $service)
    {
        return $service->finishCompetition($competition);
    }
}
