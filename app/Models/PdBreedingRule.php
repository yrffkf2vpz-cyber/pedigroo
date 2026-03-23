<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdBreedingRule extends Model
{
    protected $table = 'pd_breeding_rules';

    protected $fillable = [
        'breed_id',
        'rule_key',
        'value',
    ];
}