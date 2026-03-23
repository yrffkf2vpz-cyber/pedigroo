<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleSuggestion extends Model
{
    protected $table = 'rule_suggestions';

    protected $fillable = [
        'detected_type',
        'raw_value',
        'suggested_rule',
        'occurrences',
        'status',
        'breed_id',
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breed_id');
    }
}