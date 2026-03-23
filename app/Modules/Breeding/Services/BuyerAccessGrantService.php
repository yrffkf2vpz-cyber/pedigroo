<?php

namespace App\Modules\Breeding\Services;

use App\Models\Breeding\BuyerAccessGrant;
use App\Models\Breeding\BuyerAccessRequest;
use App\Modules\AccessControl\Services\AccessTimelineBuilder;
use App\Modules\AccessControl\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class BuyerAccessGrantService
{
    public function __construct(
        protected AccessTimelineBuilder $timeline,
        protected AuditLogService $audit
    ) {}

    /**
     * Create a grant from an approved request.
     */
    public function createGrantFromRequest(BuyerAccessRequest $request): BuyerAccessGrant
    {
        if (!$request->isApproved()) {
            throw new \Exception("Cannot create grant: request is not approved.");
        }

        return DB::transaction(function () use ($request) {

            $grant = BuyerAccessGrant::create([
                'request_id' => $request->id,
                'buyer_id'   => $request->buyer_id,
                'kennel_id'  => $request->kennel_id,
                'dog_id'     => $request->dog_id,
                'expires_at' => now()->addDays(30),
            ]);

            // Timeline
            $this->timeline->grantCreated(
                $request->buyer_id,
                $request->kennel_id,
                $request->dog_id
            );

            // Audit
            $this->audit->log('breeding_grant_created', [
                'grant_id'   => $grant->id,
                'request_id' => $request->id,
            ]);

            return $grant;
        });
    }

    /**
     * Expire a grant.
     */
    public function expireGrant(BuyerAccessGrant $grant): void
    {
        if ($grant->expires_at && $grant->expires_at->isFuture()) {
            return; // még nem járt le
        }

        $grant->update(['expired' => true]);

        $this->timeline->grantExpired(
            $grant->buyer_id,
            $grant->kennel_id,
            $grant->dog_id
        );

        $this->audit->log('breeding_grant_expired', [
            'grant_id' => $grant->id,
        ]);
    }

    /**
     * Revoke a grant manually.
     */
    public function revokeGrant(BuyerAccessGrant $grant, int $ownerId): void
    {
        $grant->update(['revoked' => true]);

        $this->timeline->grantRevoked(
            $grant->buyer_id,
            $grant->kennel_id,
            $grant->dog_id,
            $ownerId
        );

        $this->audit->log('breeding_grant_revoked', [
            'grant_id' => $grant->id,
            'owner_id' => $ownerId,
        ]);
    }
}
