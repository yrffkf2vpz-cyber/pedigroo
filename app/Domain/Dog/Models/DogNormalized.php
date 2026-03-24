<?php

namespace App\Domain\Dog\Models;

use Illuminate\Database\Eloquent\Model;

class DogNormalized extends Model
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
        'breed_id',
        'origin_country',
        'standing_country',
        'breeder_id',
        'owner_id',
        'kennel_id',
        'history_classification',
        'needs_review',
    ];

    protected $casts = [
        'dob' => 'date',
        'needs_review' => 'boolean',
    ];

    public function father()
    {
        return $this->belongsTo(self::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(self::class, 'mother_id');
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breed_id');
    }
}


