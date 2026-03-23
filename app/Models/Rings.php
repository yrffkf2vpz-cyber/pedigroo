<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rings extends Model
{
    protected $table = 'pd_rings';

    protected $fillable = [
        'number',
        'label',
        'created_at',
        'updated_at',
    ];

    public function event_resultss()
    {
        return $this->hasMany(Event_results::class);
    }

}
