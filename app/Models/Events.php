<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'pd_events';

    protected $fillable = [
        'name',
        'country',
        'city',
        'venue',
        'start_date',
        'end_date',
        'event_type',
        'organizer',
        'created_at',
        'updated_at',
    ];

    public function championshipss()
    {
        return $this->hasMany(Championships::class);
    }

    public function dog_sport_resultss()
    {
        return $this->hasMany(Dog_sport_results::class);
    }

    public function dog_working_resultss()
    {
        return $this->hasMany(Dog_working_results::class);
    }

    public function event_resultss()
    {
        return $this->hasMany(Event_results::class);
    }

    public function dogs()
    {
        return $this->belongsToMany(Dog::class);
    }

}
