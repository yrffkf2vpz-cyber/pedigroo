<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Learning_aliases extends Model
{
    protected $table = 'pd_learning_aliases';

    protected $fillable = [
        'domain',
        'alias',
        'canonical',
        'created_at',
        'updated_at',
    ];

}
