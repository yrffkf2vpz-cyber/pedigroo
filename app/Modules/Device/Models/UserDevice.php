<?php

namespace App\Modules\Device\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $table = 'pd_user_devices';

    protected $fillable = [
        'user_id',
        'device_name',
        'fingerprint',
        'is_default',
        'last_used_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function verifications()
    {
        return $this->hasMany(DeviceVerification::class, 'device_id');
    }

    // --- HELPERS ---

    public function markAsDefault(): void
    {
        $this->update(['is_default' => true]);
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function isDefault(): bool
    {
        return $this->is_default === true;
    }
}
