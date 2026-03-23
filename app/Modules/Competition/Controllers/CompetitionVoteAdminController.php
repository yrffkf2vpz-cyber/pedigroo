<?php

namespace App\Modules\Competition\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Competition\Models\CompetitionVote;

class CompetitionVoteAdminController extends Controller
{
    public function index()
    {
        return CompetitionVote::with(['entry.competition', 'user'])
            ->orderByDesc('id')
            ->get();
    }

    public function destroy(CompetitionVote $vote)
    {
        $vote->delete();

        return response()->json(['status' => 'ok']);
    }
}
