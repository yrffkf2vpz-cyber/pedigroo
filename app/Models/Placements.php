<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placements extends Model
{
    protected $table = 'pd_placements';

    protected $fillable = [
        'code',
        'label',
        'created_at',
        'updated_at',
    ];

}
