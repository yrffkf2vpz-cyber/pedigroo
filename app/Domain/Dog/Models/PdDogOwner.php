<?php

namespace App\Domain\Dog\Models;

use Illuminate\Database\Eloquent\Model;

class PdDogOwner extends Model
{
    protected $table = 'pd_dog_owners';

    protected $fillable = [
        'dog_id',
        'owner_id',
        'ownership_type', // primary, co-owner, holder
        'can_edit',
        'acquired_at',
        'released_at',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
        'acquired_at' => 'date',
        'released_at' => 'date',
    ];
}