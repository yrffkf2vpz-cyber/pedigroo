<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Support\Str;

class InviteService
{
    public function generateInvite(User $inviter): ?UserInvite
    {
        if ($inviter->invite_quota <= 0) {
            return null;
        }

        $inviter->invite_quota--;
        $inviter->save();

        return UserInvite::create([
            'inviter_id' => $inviter->id,
            'code' => Str::uuid(),
            'status' => 'pending',
        ]);
    }

    public function useInvite(string $code, User $newUser): bool
    {
        $invite = UserInvite::where('code', $code)
            ->where('status', 'pending')
            ->first();

        if (! $invite) {
            return false;
        }

        $invite->invitee_id = $newUser->id;
        $invite->status = 'used';
        $invite->used_at = now();
        $invite->save();

        $newUser->invited_by_user_id = $invite->inviter_id;
        $newUser->save();

        return true;
    }

    public function purchaseInvite(User $user, int $tokenCost = 10): ?UserInvite
    {
        $tokenService = new TokenService();

        if (! $tokenService->spend($user, $tokenCost, 'invite_purchase')) {
            return null;
        }

        return $this->generateInvite($user);
    }
}