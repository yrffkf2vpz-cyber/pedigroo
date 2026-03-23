<?php

namespace App\Modules\Competition\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionEntry extends Model
{
    protected $fillable = [
        'competition_id',
        'user_id',
        'media_type',
        'media_url',
        'caption',
        'votes_count',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function votes()
    {
        return $this->hasMany(CompetitionVote::class, 'entry_id');
    }
}
