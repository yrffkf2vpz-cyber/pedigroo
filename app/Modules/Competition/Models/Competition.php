<?php

namespace App\Modules\Competition\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'status', // upcoming, active, finished
        'is_auto_generated',
    ];

    public function category()
    {
        return $this->belongsTo(CompetitionCategory::class, 'category_id');
    }

    public function entries()
    {
        return $this->hasMany(CompetitionEntry::class);
    }
}
