<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineTask extends Model
{
    protected $fillable = [
        'type',
        'payload',
        'status',
        'log',
    ];
}