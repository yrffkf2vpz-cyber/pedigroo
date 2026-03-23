<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessRequest;

class AccessRequestService
{
    public function __construct(
        protected TrustScoreService $trustScore,
        protected SuspiciousActivityService $suspicious,
        protected AccessTimelineBuilder $timeline
    ) {}

    public function create(int $userId, int $kennelId, ?int $dogId, string $type, ?string $message = null): AccessRequest
    {
        // 1) Suspicious activity detection (ELOSZÖR)
        if ($this->suspicious->detect($userId)) {
            $this->trustScore->onSuspiciousActivity($userId);
        }

        // 2) Trust score: request created (MÁSODIK)
        $this->trustScore->onRequestCreated($userId);

        // 3) Create the request (HARMADIK)
        $request = AccessRequest::create([
            'requester_user_id' => $userId,
            'kennel_id' => $kennelId,
            'dog_id' => $dogId,
            'request_type' => $type,
            'message' => $message,
            'status' => 'pending',
        ]);

        // 4) Timeline event (VÉGÉN)
        $this->timeline->requestCreated($request);

        return $request;
    }

    public function setStatus(AccessRequest $request, string $status): AccessRequest
    {
        $request->status = $status;
        $request->save();

        return $request;
    }
}<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessRequest;

class AccessRequestService
{
    public function __construct(
        protected TrustScoreService $trustScore,
        protected SuspiciousActivityService $suspicious,
        protected AccessTimelineBuilder $timeline
    ) {}

    public function create(int $userId, int $kennelId, ?int $dogId, string $type, ?string $message = null): AccessRequest
    {
        // 1) Suspicious activity detection (ELOSZÖR)
        if ($this->suspicious->detect($userId)) {
            $this->trustScore->onSuspiciousActivity($userId);
        }

        // 2) Trust score: request created (MÁSODIK)
        $this->trustScore->onRequestCreated($userId);

        // 3) Create the request (HARMADIK)
        $request = AccessRequest::create([
            'requester_user_id' => $userId,
            'kennel_id' => $kennelId,
            'dog_id' => $dogId,
            'request_type' => $type,
            'message' => $message,
            'status' => 'pending',
        ]);
        $this->audit->log(
            userId: $userId,
            kennelId: $kennelId,
            dogId: $dogId,
            action: 'request_created'
        );

        // 4) Timeline event (VÉGÉN)
        $this->timeline->requestCreated($request);

        return $request;
    }

    public function setStatus(AccessRequest $request, string $status): AccessRequest
    {
        $request->status = $status;
        $request->save();

        return $request;
    }
}