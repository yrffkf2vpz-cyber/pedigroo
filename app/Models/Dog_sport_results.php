<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dog_sport_results extends Model
{
    protected $table = 'pd_dog_sport_results';

    protected $fillable = [
        'dog_id',
        'event_id',
        'result',
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

}
