<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessTimeline;

class AccessTimelineBuilder
{
    public function log(
        int $userId,
        ?int $kennelId,
        ?int $dogId,
        string $event,
        array $meta = []
    ): AccessTimeline {
        return AccessTimeline::create([
            'user_id'   => $userId,
            'kennel_id' => $kennelId,
            'dog_id'    => $dogId,
            'event'     => $event,
            'meta'      => $meta,
        ]);
    }

    // ---------------------------------------------------------
    // Buyer Access Events
    // ---------------------------------------------------------

    public function requestCreated(int $userId, int $kennelId, ?int $dogId): void
    {
        $this->log($userId, $kennelId, $dogId, 'request_created');
    }

    public function requestApproved(int $userId, int $kennelId, ?int $dogId): void
    {
        $this->log($userId, $kennelId, $dogId, 'request_approved');
    }

    public function requestDenied(int $userId, int $kennelId, ?int $dogId): void
    {
        $this->log($userId, $kennelId, $dogId, 'request_denied');
    }

    public function permissionExpired(int $userId, int $kennelId, ?int $dogId): void
    {
        $this->log($userId, $kennelId, $dogId, 'permission_expired');
    }

    public function overrideSet(int $userId, int $kennelId, ?int $dogId): void
    {
        $this->log($userId, $kennelId, $dogId, 'override_set');
    }

    public function overrideRemoved(int $userId, int $kennelId, ?int $dogId): void
    {
        $this->log($userId, $kennelId, $dogId, 'override_removed');
    }

    public function visibilityDenied(int $userId, int $kennelId, ?int $dogId, string $reason): void
    {
        $this->log($userId, $kennelId, $dogId, 'visibility_denied', ['reason' => $reason]);
    }

    // ---------------------------------------------------------
    // Device Events (User-level)
    // ---------------------------------------------------------

    /**
     * Verification code sent to default device.
     */
    public function deviceVerificationRequested(int $userId, int $defaultDeviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            event: 'device_verification_requested',
            meta: ['default_device_id' => $defaultDeviceId]
        );
    }

    /**
     * New device successfully added.
     */
    public function deviceAdded(int $userId, int $deviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            event: 'device_added',
            meta: ['device_id' => $deviceId]
        );
    }

    /**
     * User sets a new default device.
     */
    public function deviceSetAsDefault(int $userId, int $deviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            event: 'device_set_default',
            meta: ['device_id' => $deviceId]
        );
    }

    /**
     * Device deleted.
     */
    public function deviceDeleted(int $userId, int $deviceId): void
    {
        $this->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            event: 'device_deleted',
            meta: ['device_id' => $deviceId]
        );
    }
}
