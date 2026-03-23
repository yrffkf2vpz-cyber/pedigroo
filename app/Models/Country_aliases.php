<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country_aliases extends Model
{
    protected $table = 'pd_country_aliases';

    protected $fillable = [
        'alias',
        'canonical',
        'created_at',
        'updated_at',
    ];

}
