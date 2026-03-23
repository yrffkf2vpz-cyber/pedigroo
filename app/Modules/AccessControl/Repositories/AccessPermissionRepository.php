<?php

namespace App\Modules\AccessControl\Repositories;

use App\Models\Access\AccessPermission;

class AccessPermissionRepository
{
    public function findByRequest(int $requestId): ?AccessPermission
    {
        return AccessPermission::where('request_id', $requestId)->first();
    }

    public function forUserAndKennel(int $userId, int $kennelId)
    {
        return AccessPermission::whereHas('request', function ($q) use ($userId, $kennelId) {
            $q->where('requester_user_id', $userId)
              ->where('kennel_id', $kennelId)
              ->where('status', 'approved');
        })->get();
    }

    public function hasPermission(int $userId, int $kennelId, string $field): bool
    {
        $permission = $this->forUserAndKennel($userId, $kennelId)->first();

        if (!$permission) {
            return false;
        }

        if ($permission->expires_at && now()->greaterThan($permission->expires_at)) {
            return false;
        }

        return in_array($field, $permission->allowed_fields);
    }
}