<?php

namespace App\Modules\TrustScore\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\TrustScore\Services\TrustScoreService;
use Illuminate\Http\Request;

class TrustScoreController extends Controller
{
    public function __construct(
        protected TrustScoreService $service
    ) {}

    /**
     * Felhasználó aktuális TrustScore értéke.
     */
    public function score(Request $request)
    {
        $user = $request->user();
        $score = $this->service->getScore($user);

        return response()->json([
            'score' => $score->score,
            'level' => $score->level,
        ]);
    }

    /**
     * Felhasználó TrustScore eseményei.
     */
    public function events(Request $request)
    {
        $user = $request->user();
        $events = $this->service->getEvents($user);

        return response()->json($events);
    }

    /**
     * Admin: összes felhasználó TrustScore listázása.
     */
    public function adminList()
    {
        $users = User::with('trustScore')
            ->orderByRaw('COALESCE(trust_scores.score, 0) DESC')
            ->paginate(30);

        return response()->json($users);
    }

    /**
     * Admin: egy felhasználó TrustScore + események.
     */
    public function adminDetail($id)
    {
        $user = User::with('trustScore')->findOrFail($id);

        return response()->json([
            'user'   => $user,
            'score'  => $user->trustScore?->score ?? 0,
            'level'  => $user->trustScore?->level ?? 'Bronze',
            'events' => $this->service->getEvents($user, 200),
        ]);
    }
}
