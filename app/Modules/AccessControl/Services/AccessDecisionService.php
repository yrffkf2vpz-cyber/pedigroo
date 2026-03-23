<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessPermission;
use App\Models\Access\AccessRequest;

class AccessDecisionService
{
    public function __construct(
        protected TrustScoreService $trustScore,
        protected AccessTimelineBuilder $timeline,
        protected AuditLogService $audit
    ) {}

    public function approve(AccessRequest $request, int $ownerId, array $allowedFields, ?string $expiresAt = null): AccessPermission
    {
        // 1) Trust score: approved
        $this->trustScore->onRequestApproved($request->requester_user_id);

        // 2) Create permission
        $permission = AccessPermission::create([
            'request_id' => $request->id,
            'granted_by_user_id' => $ownerId,
            'allowed_fields' => $allowedFields,
            'expires_at' => $expiresAt,
        ]);

        // 3) Update request status
        $request->update(['status' => 'approved']);

        // 4) Timeline
        $this->timeline->requestApproved($request, $permission);

        // 5) Audit log
        $this->audit->log(
            userId: $ownerId,
            kennelId: $request->kennel_id,
            dogId: $request->dog_id,
            action: 'request_approved'
        );

        return $permission;
    }

    public function deny(AccessRequest $request, int $ownerId): AccessRequest
    {
        // 1) Trust score: denied
        $this->trustScore->onRequestDenied($request->requester_user_id);

        // 2) Update request status
        $request->update(['status' => 'denied']);

        // 3) Timeline
        $this->timeline->requestDenied($request);

        // 4) Audit log
        $this->audit->log(
            userId: $ownerId,
            kennelId: $request->kennel_id,
            dogId: $request->dog_id,
            action: 'request_denied'
        );

        return $request;
    }
}