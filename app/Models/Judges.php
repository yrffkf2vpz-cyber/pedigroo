<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Judges extends Model
{
    protected $table = 'pd_judges';

    protected $fillable = [
        'full_name',
        'last_name',
        'first_name',
        'country',
        'created_at',
        'updated_at',
    ];

    public function event_resultss()
    {
        return $this->hasMany(Event_results::class);
    }

}
