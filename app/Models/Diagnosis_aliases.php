<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosis_aliases extends Model
{
    protected $table = 'pd_diagnosis_aliases';

    protected $fillable = [
        'alias',
        'canonical',
        'created_at',
        'updated_at',
    ];

}
