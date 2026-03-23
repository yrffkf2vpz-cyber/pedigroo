<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\Model;

class UserTimeline extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
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
