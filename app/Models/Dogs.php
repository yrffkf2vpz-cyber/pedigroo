<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dogs extends Model
{
    protected $table = 'pd_dogs';

    protected $fillable = [
        'father_id',
        'mother_id',
        'name',
        'prefix',
        'firstname',
        'lastname',
        'reg_no',
        'dob',
        'sex',
        'color',
        'official_color',
        'birth_color',
        'breed',
        'origin_country',
        'standing_country',
        'breeder_id',
        'owner_id',
        'kennel_id',
        'created_at',
        'updated_at',
        'history_classification',
        'needs_review',
    ];

    public function father()
    {
        return $this->belongsTo(Father::class);
    }

    public function mother()
    {
        return $this->belongsTo(Mother::class);
    }

    public function breeder()
    {
        return $this->belongsTo(Breeder::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function kennel()
    {
        return $this->belongsTo(Kennel::class);
    }

    public function championshipss()
    {
        return $this->hasMany(Championships::class);
    }

    public function dog_behavior_resultss()
    {
        return $this->hasMany(Dog_behavior_results::class);
    }

    public function dog_event_resultss()
    {
        return $this->hasMany(Dog_event_results::class);
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

    public function familiess()
    {
        return $this->hasMany(Families::class);
    }

    public function health_recordss()
    {
        return $this->hasMany(Health_records::class);
    }

    public function parentss()
    {
        return $this->hasMany(Parents::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function parents()
    {
        return $this->belongsToMany(Parent::class);
    }

}
