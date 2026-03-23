<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class UserTrustScore extends Model
{
    protected $table = 'pd_user_trust_score';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'score',
        'level',
        'last_update',
    ];

    protected $casts = [
        'last_update' => 'datetime',
    ];
}