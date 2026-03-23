<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class AccessAuditLog extends Model
{
    protected $table = 'pd_access_audit_log';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'kennel_id',
        'dog_id',
        'action',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}