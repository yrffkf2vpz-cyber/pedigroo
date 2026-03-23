<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessPermission;
use App\Models\Access\UserVisibilityOverride;
use App\Models\Access\UserTrustScore;

class VisibilityService
{
    public function __construct(
        protected OverrideService $overrideService,
        protected AuditLogService $audit
    ) {}

    public function canView(int $userId, int $kennelId, string $field): bool
    {
        // 0) Audit: minden ellenorzést naplózunk
        $this->audit->log(
            userId: $userId,
            kennelId: $kennelId,
            dogId: null,
            action: 'visibility_check'
        );

        // 1) OWNER OVERRIDE (LEGELSO)
        $override = $this->overrideService->getOverride($userId, $kennelId);

        if ($override && in_array($field, $override->allowed_fields)) {
            return true;
        }

        // 2) ACTIVE PERMISSION
        $permission = AccessPermission::whereHas('request', function ($q) use ($userId, $kennelId) {
                $q->where('requester_user_id', $userId)
                  ->where('kennel_id', $kennelId)
                  ->where('status', 'approved');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($permission && in_array($field, $permission->allowed_fields)) {
            return true;
        }

        // 3) TRUST SCORE
        $trust = UserTrustScore::where('user_id', $userId)->first();

        if ($trust && $trust->level === 'red') {
            // audit: megtagadva
            $this->audit->log(
                userId: $userId,
                kennelId: $kennelId,
                dogId: null,
                action: 'visibility_denied'
            );
            return false;
        }

        // 4) YELLOW ? csak explicit permission vagy override
        if ($trust && $trust->level === 'yellow') {
            // nincs permission ? deny
            $this->audit->log(
                userId: $userId,
                kennelId: $kennelId,
                dogId: null,
                action: 'visibility_denied'
            );
            return false;
        }

        // 5) DEFAULT DENY
        $this->audit->log(
            userId: $userId,
            kennelId: $kennelId,
            dogId: null,
            action: 'visibility_denied'
        );

        return false;
    }
}