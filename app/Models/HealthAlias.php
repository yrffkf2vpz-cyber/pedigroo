<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthAlias extends Model
{
    protected $table = 'health_aliases';

    protected $fillable = [
        'test_type',
        'alias',
        'canonical',
        'countries',
    ];

    protected $casts = [
        'countries' => 'array',
    ];
}