<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test_table extends Model
{
    protected $table = 'pd_test_table';

    protected $fillable = [
        'name',
        'age',
        'is_active',
        'created_at',
        'updated_at',
    ];

}
