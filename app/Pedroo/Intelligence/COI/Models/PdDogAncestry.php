<?php

namespace App\Pedroo\Intelligence\COI\Models;

use Illuminate\Database\Eloquent\Model;

class PdDogAncestry extends Model
{
    protected $table = 'pd_dog_ancestry';

    public $timestamps = false;

    protected $fillable = [
        'dog_id',
        'ancestor_id',
        'generations',
    ];
}