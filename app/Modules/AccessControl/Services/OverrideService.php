<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\UserVisibilityOverride;

class OverrideService
{
    public function setOverride(int $userId, int $kennelId, array $fields): UserVisibilityOverride
    {
        return UserVisibilityOverride::updateOrCreate(
            [
                'user_id' => $userId,
                'kennel_id' => $kennelId,
            ],
            [
                'allowed_fields' => $fields,
            ]
        );
    }

    public function removeOverride(int $userId, int $kennelId): void
    {
        UserVisibilityOverride::where('user_id', $userId)
            ->where('kennel_id', $kennelId)
            ->delete();
    }

    public function getOverride(int $userId, int $kennelId): ?UserVisibilityOverride
    {
        return UserVisibilityOverride::where('user_id', $userId)
            ->where('kennel_id', $kennelId)
            ->first();
    }
}