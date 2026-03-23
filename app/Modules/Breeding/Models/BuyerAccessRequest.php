<?php

namespace App\Modules\Breeding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerAccessRequest extends Model
{
    protected $table = 'pd_buyer_access_requests';

    protected $fillable = [
        'buyer_id',
        'dog_id',
        'kennel_id',
        'purpose',
        'message',
        'status',
        'ip_address',
        'device_fingerprint',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'buyer_id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dog::class, 'dog_id');
    }

    public function kennel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kennel::class, 'kennel_id');
    }

    public function grant()
    {
        return $this->hasOne(BuyerAccessGrant::class, 'request_id');
    }

    // --- HELPERS ---

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDenied(): bool
    {
    return $this->status === 'denied';
    }

}