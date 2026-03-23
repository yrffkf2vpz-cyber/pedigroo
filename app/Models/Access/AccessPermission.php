<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class AccessPermission extends Model
{
    protected $table = 'pd_access_permissions';

    protected $fillable = [
        'request_id',
        'granted_by_user_id',
        'allowed_fields',
        'expires_at',
    ];

    protected $casts = [
        'allowed_fields' => 'array',
        'expires_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(AccessRequest::class, 'request_id');
    }

    public function grantedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'granted_by_user_id');
    }
}