<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessAuditLog;

class AuditService
{
    public function log(int $userId, int $kennelId, ?int $dogId, string $action, ?string $reason = null): void
    {
        AccessAuditLog::create([
            'user_id' => $userId,
            'kennel_id' => $kennelId,
            'dog_id' => $dogId,
            'action' => $action,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}