<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Learning_queue extends Model
{
    protected $table = 'pd_learning_queue';

    protected $fillable = [
        'domain',
        'raw_input',
        'normalized_input',
        'ai_suggestion',
        'context',
        'status',
        'count',
        'first_seen_at',
        'last_seen_at',
        'created_at',
        'updated_at',
    ];

}
