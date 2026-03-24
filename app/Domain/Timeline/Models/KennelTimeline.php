<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\Model;

class KennelTimeline extends Model
{
    protected $table = 'pd_kennel_timeline';

    protected $fillable = [
        'kennel_id',
        'event_type',
        'timestamp',
        'data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'data' => 'array',
    ];

    public function kennel()
    {
        return $this->belongsTo(\App\Models\Kennel::class);
    }
}