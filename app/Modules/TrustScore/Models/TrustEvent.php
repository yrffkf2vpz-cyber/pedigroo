<?php

namespace App\Modules\TrustScore\Models;

use Illuminate\Database\Eloquent\Model;

class TrustEvent extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
