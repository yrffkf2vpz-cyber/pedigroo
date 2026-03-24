<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\Model;

class ClubTimeline extends Model
{
    protected $table = 'pd_club_timeline';

    protected $fillable = [
        'club_id',
        'event_type',
        'timestamp',
        'data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'data' => 'array',
    ];

    public function club()
    {
        return $this->belongsTo(\App\Models\Club::class);
    }
}