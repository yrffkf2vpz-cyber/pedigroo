<?php

namespace App\Services;

use App\Models\User;
use App\Models\TokenWallet;
use App\Models\TokenTransaction;

class TokenService
{
    public function earn(User $user, int $amount, string $reason, array $meta = []): void
    {
        $wallet = TokenWallet::firstOrCreate(['user_id' => $user->id]);

        $wallet->balance += $amount;
        $wallet->save();

        $user->token_lifetime += $amount;
        $user->token_level = $this->calculateLevel($user->token_lifetime);
        $user->save();

        TokenTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'reason' => $reason,
            'meta' => $meta,
        ]);
    }

    public function spend(User $user, int $amount, string $reason, array $meta = []): bool
    {
        $wallet = TokenWallet::firstOrCreate(['user_id' => $user->id]);

        if ($wallet->balance < $amount) {
            return false;
        }

        $wallet->balance -= $amount;
        $wallet->save();

        TokenTransaction::create([
            'user_id' => $user->id,
            'amount' => -$amount,
            'reason' => $reason,
            'meta' => $meta,
        ]);

        return true;
    }

    private function calculateLevel(int $lifetime): string
    {
        return match (true) {
            $lifetime >= 2000 => 'platinum',
            $lifetime >= 500  => 'gold',
            $lifetime >= 100  => 'silver',
            default           => 'bronze',
        };
    }
}