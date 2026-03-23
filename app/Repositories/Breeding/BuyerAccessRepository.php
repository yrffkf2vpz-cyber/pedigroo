<?php

namespace App\Repositories\Breeding;

use App\Models\Breeding\BuyerAccessRequest;
use App\Models\Breeding\BuyerAccessGrant;

class BuyerAccessRepository
{
    // --- REQUESTS ---

    public function findById(int $id): ?BuyerAccessRequest
    {
        return BuyerAccessRequest::with(['buyer', 'dog', 'kennel', 'grant'])
            ->find($id);
    }

    public function findPendingByKennel(int $kennelId)
    {
        return BuyerAccessRequest::where('kennel_id', $kennelId)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
    }

    public function findByBuyerAndDog(int $buyerId, int $dogId): ?BuyerAccessRequest
    {
        return BuyerAccessRequest::where('buyer_id', $buyerId)
            ->where('dog_id', $dogId)
            ->latest()
            ->first();
    }

    public function listForAdmin(int $limit = 50)
    {
        return BuyerAccessRequest::with(['buyer', 'dog', 'kennel'])
            ->orderByDesc('created_at')
            ->paginate($limit);
    }

    public function listForKennelDashboard(int $kennelId, int $limit = 20)
    {
        return BuyerAccessRequest::with(['buyer', 'dog'])
            ->where('kennel_id', $kennelId)
            ->orderByDesc('created_at')
            ->paginate($limit);
    }

    // --- GRANTS ---

    public function findActiveGrant(int $buyerId, int $dogId): ?BuyerAccessGrant
    {
        return BuyerAccessGrant::where('buyer_id', $buyerId)
            ->where('dog_id', $dogId)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function listByBuyer(int $buyerId)
    {
        return BuyerAccessGrant::with(['dog', 'kennel'])
            ->where('buyer_id', $buyerId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function listByKennel(int $kennelId)
    {
        return BuyerAccessGrant::with(['buyer', 'dog'])
            ->where('kennel_id', $kennelId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function findExpiredGrants()
    {
        return BuyerAccessGrant::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();
    }
}