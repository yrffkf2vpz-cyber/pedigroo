<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'pd_locations';

    protected $fillable = [
        'country',
        'city',
        'venue',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
    ];

}
