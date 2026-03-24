<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Dog extends Model
{
    protected $table = 'pd_dogs';

    protected $fillable = [
        'name',
        'reg_no',
        'breed_id',
        'sex',
        'birth_date',
        'kennel_name',
        'breeder_name',
        'owner_name',
        'color',
        'pattern',
        'status',
        'sire_id',
        'dam_id',
    ];

    protected $dates = ['birth_date'];

    public function sire()
    {
        return $this->belongsTo(Dog::class, 'sire_id');
    }

    public function dam()
    {
        return $this->belongsTo(Dog::class, 'dam_id');
    }

    public function litters()
    {
        return $this->hasMany(Litter::class, 'dam_id');
    }

    public function exams()
    {
        return $this->hasMany(DogExam::class);
    }

    public function healthResults()
    {
        return $this->hasMany(DogHealthResult::class);
    }

    public function genotypes()
    {
        return $this->hasMany(DogGenotype::class);
    }

    public function ageInMonths(): int
    {
        return $this->birth_date
            ? $this->birth_date->diffInMonths(Carbon::now())
            : 0;
    }
}