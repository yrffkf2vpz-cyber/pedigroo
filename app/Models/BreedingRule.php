<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreedingRule extends Model
{
    protected $table = 'pd_breeding_rules';

    protected $fillable = [
        'breed_id',
        'rule_key',
        'value',
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breed_id');
    }
}
