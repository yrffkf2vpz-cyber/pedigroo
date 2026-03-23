<?php

namespace App\Modules\Breeding\Services;

use App\Modules\Breeding\Models\BuyerAccessRequest;
use App\Modules\Breeding\Models\BuyerAccessGrant;
use App\Modules\AccessControl\Services\AccessDecisionService;
use App\Modules\AccessControl\Services\AccessTimelineBuilder;
use App\Modules\AccessControl\Services\AuditLogService;
use App\Models\Kennel;
use Illuminate\Support\Facades\DB;

class BuyerAccessRequestService
{
    public function __construct(
        protected AccessDecisionService $decision,
        protected AccessTimelineBuilder $timeline,
        protected AuditLogService $audit
    ) {}

    /**
     * Buyer creates a new access request.
     */
    public function createRequest(
        int $buyerId,
        int $kennelId,
        ?int $dogId,
        ?string $purpose,
        ?string $message,
        string $ipAddress,
        ?string $deviceFingerprint
    ): BuyerAccessRequest {

        // 1) Anti-abuse: duplikált pending request tiltása
        if (BuyerAccessRequest::where('buyer_id', $buyerId)
            ->where('kennel_id', $kennelId)
            ->where('status', 'pending')
            ->exists()) {
            throw new \Exception("You already have a pending request for this kennel.");
        }

        // 2) AccessControl döntés
        $decision = $this->decision->canRequest($buyerId, $kennelId, $dogId);

        if (!$decision->allowed) {
            $this->timeline->visibilityDenied($buyerId, $kennelId, $dogId, $decision->reason);

            $this->audit->log('breeding_request_denied_by_policy', [
                'buyer_id' => $buyerId,
                'kennel_id' => $kennelId,
                'dog_id' => $dogId,
                'reason' => $decision->reason,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $deviceFingerprint,
            ]);

            throw new \Exception("Request denied: {$decision->reason}");
        }

        // 3) Request létrehozása
        $request = BuyerAccessRequest::create([
            'buyer_id' => $buyerId,
            'kennel_id' => $kennelId,
            'dog_id' => $dogId,
            'purpose' => $purpose,
            'message' => $message,
            'status' => 'pending',
            'ip_address' => $ipAddress,
            'device_fingerprint' => $deviceFingerprint,
        ]);

        // 4) Timeline
        $this->timeline->requestCreated($buyerId, $kennelId, $dogId);

        // 5) Audit
        $this->audit->log('breeding_request_created', [
            'request_id' => $request->id,
            'buyer_id' => $buyerId,
            'kennel_id' => $kennelId,
            'dog_id' => $dogId,
            'ip_address' => $ipAddress,
            'device_fingerprint' => $deviceFingerprint,
        ]);

        return $request;
    }

    /**
     * Kennel owner approves a request.
     */
    public function approveRequest(BuyerAccessRequest $request, int $ownerId): BuyerAccessGrant
    {
        // 1) Jogosultság ellenorzés
        if (!Kennel::where('id', $request->kennel_id)->where('owner_id', $ownerId)->exists()) {
            throw new \Exception("You do not have permission to approve this request.");
        }

        // 2) Status workflow védelem
        if ($request->status !== 'pending') {
            throw new \Exception("Only pending requests can be approved.");
        }

        return DB::transaction(function () use ($request, $ownerId) {

            // 3) Request státusz frissítése
            $request->update(['status' => 'approved']);

            // 4) Grant létrehozása
            $grant = BuyerAccessGrant::create([
                'buyer_id' => $request->buyer_id,
                'kennel_id' => $request->kennel_id,
                'dog_id' => $request->dog_id,
                'expires_at' => now()->addDays(30),
            ]);

            // 5) Timeline
            $this->timeline->requestApproved($request->buyer_id, $request->kennel_id, $request->dog_id);

            // 6) Audit
            $this->audit->log('breeding_request_approved', [
                'request_id' => $request->id,
                'owner_id' => $ownerId,
            ]);

            return $grant;
        });
    }

    /**
     * Kennel owner denies a request.
     */
    public function denyRequest(BuyerAccessRequest $request, int $ownerId): void
    {
        // 1) Jogosultság ellenorzés
        if (!Kennel::where('id', $request->kennel_id)->where('owner_id', $ownerId)->exists()) {
            throw new \Exception("You do not have permission to deny this request.");
        }

        // 2) Status workflow védelem
        if ($request->status !== 'pending') {
            throw new \Exception("Only pending requests can be denied.");
        }

        // 3) Státusz frissítés
        $request->update(['status' => 'denied']);

        // 4) Timeline
        $this->timeline->requestDenied($request->buyer_id, $request->kennel_id, $request->dog_id);

        // 5) Audit
        $this->audit->log('breeding_request_denied', [
            'request_id' => $request->id,
            'owner_id' => $ownerId,
        ]);
    }
}

