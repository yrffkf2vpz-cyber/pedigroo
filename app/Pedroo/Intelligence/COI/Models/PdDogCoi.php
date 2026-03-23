<?php

namespace App\Pedroo\Intelligence\COI\Models;

use Illuminate\Database\Eloquent\Model;

class PdDogCoi extends Model
{
    protected $table = 'pd_dog_coi';

    public $timestamps = false;

    protected $fillable = [
        'dog_id',
        'coi',
        'calculated_at',
    ];
}