<?php

namespace App\Modules\Competition\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'auto_generate', // AI / SystemScanner használja
    ];
}
