<?php

namespace App\Modules\Invitation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Invitation extends Model
{
    protected $fillable = [
        'token',
        'inviter_id',
        'invited_user_id',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    /**
     * Meghívót generálunk egyedi tokennel.
     */
    public static function generate(int $inviterId, ?Carbon $expiresAt = null): self
    {
        return self::create([
            'token'      => Str::uuid()->toString(),
            'inviter_id' => $inviterId,
            'expires_at' => $expiresAt ?? now()->addDays(7),
        ]);
    }

    /**
     * Meghívó érvényes-e.
     */
    public function isValid(): bool
    {
        if ($this->used_at !== null) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Meghívó felhasználása.
     */
    public function markAsUsed(int $userId): void
    {
        $this->update([
            'invited_user_id' => $userId,
            'used_at'         => now(),
        ]);
    }

    /**
     * Kapcsolatok
     */
    public function inviter()
    {
        return $this->belongsTo(\App\Models\User::class, 'inviter_id');
    }

    public function invitedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'invited_user_id');
    }
}
