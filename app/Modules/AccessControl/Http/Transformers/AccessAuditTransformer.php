<?php

namespace App\Modules\AccessControl\Http\Transformers;

use App\Models\Access\AccessAuditLog;

class AccessAuditTransformer
{
    public static function item(AccessAuditLog $log): array
    {
        return [
            'id' => $log->id,
            'user_id' => $log->user_id,
            'kennel_id' => $log->kennel_id,
            'dog_id' => $log->dog_id,
            'action' => $log->action,
            'reason' => $log->reason,
            'created_at' => $log->created_at?->toIso8601String(),
        ];
    }

    public static function collection($logs): array
    {
        return $logs->map(fn($l) => self::item($l))->toArray();
    }
}