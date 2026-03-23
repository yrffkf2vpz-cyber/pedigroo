<?php

namespace App\Modules\TrustScore\Models;

use Illuminate\Database\Eloquent\Model;

class TrustScore extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'level',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function events()
    {
        return $this->hasMany(TrustEvent::class);
    }

    /**
     * Szint meghatározása score alapján.
     */
    public static function levelFor(int $score): string
    {
        return match (true) {
            $score >= 700 => 'Platinum',
            $score >= 300 => 'Gold',
            $score >= 100 => 'Silver',
            default       => 'Bronze',
        };
    }
}
