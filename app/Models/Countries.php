<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $table = 'pd_countries';

    protected $fillable = [
        'code',
        'name',
        'date_format',
        'created_at',
        'updated_at',
    ];

}
