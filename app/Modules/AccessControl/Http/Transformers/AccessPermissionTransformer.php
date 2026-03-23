<?php

namespace App\Modules\AccessControl\Http\Transformers;

use App\Models\Access\AccessPermission;

class AccessPermissionTransformer
{
    public static function item(AccessPermission $permission): array
    {
        return [
            'id' => $permission->id,
            'request_id' => $permission->request_id,
            'granted_by_user_id' => $permission->granted_by_user_id,
            'allowed_fields' => $permission->allowed_fields,
            'expires_at' => $permission->expires_at?->toIso8601String(),
            'created_at' => $permission->created_at?->toIso8601String(),
            'updated_at' => $permission->updated_at?->toIso8601String(),
        ];
    }

    public static function collection($permissions): array
    {
        return $permissions->map(fn($p) => self::item($p))->toArray();
    }
}