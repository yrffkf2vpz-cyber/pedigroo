<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedFile extends Model
{
    protected $fillable = [
        'module',
        'task',
        'file_path',
        'hash',
    ];
}
