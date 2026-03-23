<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'pd_countries';

    protected $fillable = [
        'code',
        'name',
        'date_format',
    ];
}