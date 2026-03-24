<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\Model;

class BreederTimeline extends Model
{
    protected $table = 'pd_breeder_timeline';

    protected $fillable = [
        'breeder_id',
        'event_type',
        'timestamp',
        'data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'data' => 'array',
    ];

    public function breeder()
    {
        return $this->belongsTo(\App\Models\Breeder::class);
    }
}