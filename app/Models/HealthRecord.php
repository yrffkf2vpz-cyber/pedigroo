<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    protected $table = 'pedroo_health_records';

    protected $fillable = [
        'dog_id',
        'type',
        'value',
        'date',
        'lab',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }
}