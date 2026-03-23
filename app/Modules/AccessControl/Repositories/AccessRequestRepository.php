<?php

namespace App\Modules\AccessControl\Repositories;

use App\Models\Access\AccessRequest;

class AccessRequestRepository
{
    public function find(int $id): ?AccessRequest
    {
        return AccessRequest::find($id);
    }

    public function forKennel(int $kennelId, int $limit = 50)
    {
        return AccessRequest::where('kennel_id', $kennelId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function forUser(int $userId, int $limit = 50)
    {
        return AccessRequest::where('requester_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function pendingForKennel(int $kennelId)
    {
        return AccessRequest::where('kennel_id', $kennelId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}