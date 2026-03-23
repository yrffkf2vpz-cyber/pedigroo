<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $table = 'pd_access_requests';

    protected $fillable = [
        'requester_user_id',
        'kennel_id',
        'dog_id',
        'request_type',
        'message',
        'status',
    ];

    public function requester()
    {
        return $this->belongsTo(\App\Models\User::class, 'requester_user_id');
    }

    public function kennel()
    {
        return $this->belongsTo(\App\Models\Kennel::class, 'kennel_id');
    }

    public function dog()
    {
        return $this->belongsTo(\App\Models\Dog::class, 'dog_id');
    }

    public function permission()
    {
        return $this->hasOne(AccessPermission::class, 'request_id');
    }
}