<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedroo_registry extends Model
{
    protected $table = 'pd_pedroo_registry';

    protected $fillable = [
        'entity_type',
        'entity_name',
        'module',
        'status',
        'details',
        'created_at',
        'updated_at',
    ];

}
