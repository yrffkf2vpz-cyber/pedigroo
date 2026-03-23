<?php

namespace App\Modules\Invitation\Services;

use App\Models\User;
use App\Modules\Invitation\Models\Invitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class InvitationService
{
    /**
     * Meghívó generálása.
     */
    public function generate(User $inviter, int $count = 1): array
    {
        $invitations = [];

        for ($i = 0; $i < $count; $i++) {
            $invitations[] = Invitation::generate($inviter->id);
        }

        // Audit log majd késobb jön
        return $invitations;
    }

    /**
     * Meghívó érvényesítése token alapján.
     */
    public function validateToken(string $token): Invitation
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            throw ValidationException::withMessages([
                'token' => 'A meghívó nem létezik.',
            ]);
        }

        if (!$invitation->isValid()) {
            throw ValidationException::withMessages([
                'token' => 'A meghívó lejárt vagy már felhasználták.',
            ]);
        }

        return $invitation;
    }

    /**
     * Meghívó felhasználása ? user létrehozása.
     */
    public function acceptInvitation(string $token, array $data): User
    {
        return DB::transaction(function () use ($token, $data) {

            $invitation = $this->validateToken($token);

            // User létrehozása
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'breed_id' => $invitation->inviter->breed_id, // ha fajta admin hívta meg
                'invited_by' => $invitation->inviter_id,
            ]);

            // Meghívó felhasználása
            $invitation->markAsUsed($user->id);

            // Új user kap 3 meghívót
            $this->generate($user, 3);

            // Reputációs elokészítés (késobb)
            // TrustScoreService::recordInvitationAccepted($invitation, $user);

            return $user;
        });
    }

    /**
     * Meghívó lejáratának ellenorzése (cron vagy event).
     */
    public function expireOldInvitations(): int
    {
        return Invitation::whereNull('used_at')
            ->where('expires_at', '<', now())
            ->update(['expires_at' => now()]);
    }
}
