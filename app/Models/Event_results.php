<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event_results extends Model
{
    protected $table = 'pd_event_results';

    protected $fillable = [
        'dog_id',
        'event_id',
        'class_type',
        'placement',
        'qualification',
        'judge_id',
        'ring_id',
        'source',
        'external_id',
        'hash',
        'raw',
        'submitted_by',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function judge()
    {
        return $this->belongsTo(Judge::class);
    }

    public function ring()
    {
        return $this->belongsTo(Ring::class);
    }

    public function external()
    {
        return $this->belongsTo(External::class);
    }

}
