<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdBreedRulesHealth extends Model
{
    protected $table = 'pd_breed_rules_health';

    protected $fillable = [
        'breed_id',
        'test_type',
        'min_result',
        'max_result',
        'mandatory',
    ];

    public $timestamps = false;
}