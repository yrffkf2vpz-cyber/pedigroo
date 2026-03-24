<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\Model;

class DogTimelineGenerated extends Model
{
    protected $table = 'pd_dog_timeline_generated';

    protected $fillable = [
        'dog_id',
        'event_type',
        'timestamp',
        'data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'data' => 'array',
    ];

    public function dog()
    {
        return $this->belongsTo(\App\Models\Dog::class);
    }
}