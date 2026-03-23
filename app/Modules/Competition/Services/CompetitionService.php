<?php

namespace App\Modules\Competition\Services;

use App\Modules\Competition\Models\Competition;
use App\Modules\Competition\Models\CompetitionEntry;
use App\Modules\Competition\Models\CompetitionVote;
use App\Services\TimelineService;
use App\Services\TokenService;
use App\Services\TrustScoreService;

class CompetitionService
{
    public function createCompetition(array $data)
    {
        return Competition::create($data);
    }

    public function enterCompetition(Competition $competition, $user, array $data)
    {
        return CompetitionEntry::create([
            'competition_id' => $competition->id,
            'user_id'        => $user->id,
            'media_type'     => $data['media_type'],
            'media_url'      => $data['media_url'],
            'caption'        => $data['caption'] ?? null,
        ]);
    }

    public function vote(CompetitionEntry $entry, $user)
    {
        CompetitionVote::create([
            'entry_id' => $entry->id,
            'user_id'  => $user->id,
        ]);

        $entry->increment('votes_count');
    }

    public function finishCompetition(Competition $competition)
    {
        $winner = $competition->entries()->orderByDesc('votes_count')->first();

        if (!$winner) {
            return null;
        }

        // Token jutalom
        app(TokenService::class)->reward($winner->user, 100, 'competition_win');

        // TrustScore jutalom
        app(TrustScoreService::class)->addScore($winner->user, 20, 'competition_win');

        // Timeline
        app(TimelineService::class)->recordUserTokenEvent(
            $winner->user,
            'Verseny megnyerve',
            "Megnyerted a {$competition->title} versenyt!",
            ['competition_id' => $competition->id]
        );

        $competition->update(['status' => 'finished']);

        return $winner;
    }
}
