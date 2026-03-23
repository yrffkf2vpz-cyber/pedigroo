<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed_rules_health extends Model
{
    protected $table = 'pd_breed_rules_health';

    protected $fillable = [
        'breed_id',
        'test_type',
        'min_result',
        'max_result',
        'mandatory',
        'created_at',
        'updated_at',
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

}
