<?php

namespace App\Modules\Token\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = [
        'user_id',
        'amount',      // mindig pozitív
        'type',        // reward | spend
        'reason',
    ];

    /**
     * Kapcsolat: a token egy userhez tartozik.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope: csak jutalmak.
     */
    public function scopeRewards($query)
    {
        return $query->where('type', 'reward');
    }

    /**
     * Scope: csak költések.
     */
    public function scopeSpends($query)
    {
        return $query->where('type', 'spend');
    }

    /**
     * User token egyenlegének kiszámítása.
     */
    public static function balanceFor(int $userId): int
    {
        $rewards = self::where('user_id', $userId)
            ->where('type', 'reward')
            ->sum('amount');

        $spends = self::where('user_id', $userId)
            ->where('type', 'spend')
            ->sum('amount');

        return $rewards - $spends;
    }
}
