<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualifications extends Model
{
    protected $table = 'pd_qualifications';

    protected $fillable = [
        'code',
        'label',
        'created_at',
        'updated_at',
    ];

}
