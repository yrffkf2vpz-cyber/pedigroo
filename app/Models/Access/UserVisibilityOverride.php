<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class UserVisibilityOverride extends Model
{
    protected $table = 'pd_user_visibility_overrides';

    protected $fillable = [
        'user_id',
        'kennel_id',
        'allowed_fields',
    ];

    protected $casts = [
        'allowed_fields' => 'array',
    ];
}