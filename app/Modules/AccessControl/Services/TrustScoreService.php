<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessAuditLog;

class TrustScoreService
{
    public function log(
        int $userId,
        ?int $kennelId,
        ?int $dogId,
        string $action,
        ?string $reason = null,
        array $meta = []
    ): AccessAuditLog {
        return AccessAuditLog::create([
            'user_id'   => $userId,
            'kennel_id' => $kennelId,
            'dog_id'    => $dogId,
            'action'    => $action,
            'reason'    => $reason,
            'meta'      => $meta,
        ]);
    }

    // ---------------------------------------------------------
    // DEVICE EVENTS (user-level)
    // ---------------------------------------------------------

    public function deviceVerificationRequested(int $userId, int $defaultDeviceId, string $code): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            action: 'device_verification_requested',
            reason: null,
            meta: [
                'default_device_id' => $defaultDeviceId,
                'code' => $code,
            ]
        );
    }

    public function deviceAdded(int $userId, int $deviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            action: 'device_added',
            reason: null,
            meta: ['device_id' => $deviceId]
        );
    }

    public function deviceSetDefault(int $userId, int $deviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            action: 'device_set_default',
            reason: null,
            meta: ['device_id' => $deviceId]
        );
    }

    public function deviceDeleted(int $userId, int $deviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            action: 'device_deleted',
            reason: null,
            meta: ['device_id' => $deviceId]
        );
    }
}
