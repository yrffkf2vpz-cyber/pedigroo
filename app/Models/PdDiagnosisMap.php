<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdDiagnosisMap extends Model
{
    protected $table = 'pd_diagnosis_map';

    protected $fillable = [
        'breed_code',
        'raw_value',
        'normalized_code',
        'category',
        'is_active',
    ];
}