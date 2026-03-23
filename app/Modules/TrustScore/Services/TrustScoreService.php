<?php

namespace App\Modules\TrustScore\Services;

use App\Models\User;
use App\Modules\TrustScore\Models\TrustScore;
use App\Modules\TrustScore\Models\TrustEvent;
use Illuminate\Support\Facades\DB;

class TrustScoreService
{
    /**
     * Növeli a felhasználó TrustScore értékét és rögzíti az eseményt.
     */
    public function addScore(User $user, int $amount, string $type, array $meta = []): TrustEvent
    {
        return DB::transaction(function () use ($user, $amount, $type, $meta) {

            // 1) TrustEvent rögzítése
            $event = TrustEvent::create([
                'user_id' => $user->id,
                'type'    => $type,
                'amount'  => $amount,
                'meta'    => $meta,
            ]);

            // 2) TrustScore frissítése
            $score = TrustScore::firstOrCreate(
                ['user_id' => $user->id],
                ['score' => 0, 'level' => 'Bronze']
            );

            $score->score += $amount;

            // 3) Szint frissítése
            $score->level = TrustScore::levelFor($score->score);
            $score->save();

            // ? 4) Timeline integráció
            app(\App\Services\TimelineService::class)
                ->recordUserTrustEvent($user, $event);

            // 5) Pedroo Intelligence hook (késobb)
            // PedrooAI::analyzeTrustEvent($event);

            return $event;
        });
    }

    /**
     * Meghívás elfogadása ? +20 TrustScore.
     */
    public function invitationAccepted(User $user): TrustEvent
    {
        return $this->addScore($user, 20, 'invitation', [
            'message' => 'Meghívás elfogadva',
        ]);
    }

    /**
     * Token örökbeadás ? +10 TrustScore.
     */
    public function tokenGiven(User $from, User $to, int $amount): TrustEvent
    {
        return $this->addScore($from, 10, 'token_give', [
            'to_user_id' => $to->id,
            'amount'     => $amount,
        ]);
    }

    /**
     * Token kölcsönadás ? +5 TrustScore.
     */
    public function tokenLoaned(User $from, User $to, int $amount): TrustEvent
    {
        return $this->addScore($from, 5, 'token_loan', [
            'to_user_id' => $to->id,
            'amount'     => $amount,
        ]);
    }

    /**
     * Kölcsön visszafizetése ? +3 TrustScore.
     */
    public function tokenRepaid(User $borrower, User $lender, int $amount): TrustEvent
    {
        return $this->addScore($borrower, 3, 'token_repay', [
            'lender_id' => $lender->id,
            'amount'    => $amount,
        ]);
    }

    /**
     * Napi aktivitás ? +1 TrustScore.
     */
    public function dailyActivity(User $user): TrustEvent
    {
        return $this->addScore($user, 1, 'activity', [
            'message' => 'Napi aktivitás',
        ]);
    }

    /**
     * AI pozitív jelzés ? +X TrustScore.
     */
    public function aiPositive(User $user, int $amount, array $meta = []): TrustEvent
    {
        return $this->addScore($user, $amount, 'ai_positive', $meta);
    }

    /**
     * AI semleges jelzés ? 0 TrustScore (csak event).
     */
    public function aiNeutral(User $user, array $meta = []): TrustEvent
    {
        return $this->addScore($user, 0, 'ai_neutral', $meta);
    }

    /**
     * TrustScore lekérése.
     */
    public function getScore(User $user): TrustScore
    {
        return TrustScore::firstOrCreate(
            ['user_id' => $user->id],
            ['score' => 0, 'level' => 'Bronze']
        );
    }

    /**
     * TrustScore események listázása.
     */
    public function getEvents(User $user, int $limit = 50)
    {
        return TrustEvent::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

