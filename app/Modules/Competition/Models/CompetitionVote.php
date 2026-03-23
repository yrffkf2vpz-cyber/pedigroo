<?php

namespace App\Modules\Competition\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionVote extends Model
{
    protected $fillable = [
        'entry_id',
        'user_id',
    ];

    public function entry()
    {
        return $this->belongsTo(CompetitionEntry::class);
    }
}
