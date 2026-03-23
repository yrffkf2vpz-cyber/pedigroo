<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'pd_classes';

    protected $fillable = [
        'code',
        'label',
        'created_at',
        'updated_at',
    ];

}
