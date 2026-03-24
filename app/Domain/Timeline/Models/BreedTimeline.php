<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\Model;

class BreedTimeline extends Model
{
    protected $table = 'pd_breed_timeline'; // ha átneveztük

    protected $fillable = [
        'breed_id',
        'event_type',
        'timestamp',
        'data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'data' => 'array',
    ];

    public function breed()
    {
        return $this->belongsTo(\App\Models\Breed::class);
    }
}