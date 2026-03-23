<?php

namespace App\Modules\Breeding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerAccessGrant extends Model
{
    protected $table = 'pd_buyer_access_grants';

    protected $fillable = [
        'request_id',
        'buyer_id',
        'dog_id',
        'kennel_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function request(): BelongsTo
    {
        return $this->belongsTo(BuyerAccessRequest::class, 'request_id');
    }

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

    // --- HELPERS ---

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->greaterThan($this->expires_at);
    }
}