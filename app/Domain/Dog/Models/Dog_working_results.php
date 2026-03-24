<?php

namespace App\Domain\Dog\Models;

use Illuminate\Database\Eloquent\Model;

class Dog_working_results extends Model
{
    protected $table = 'pd_dog_working_results';

    protected $fillable = [
        'dog_id',
        'event_id',
        'score',
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
