<?php

namespace App\Modules\AccessControl\Http\Transformers;

use App\Models\Access\AccessRequest;

class AccessRequestTransformer
{
    public static function item(AccessRequest $request): array
    {
        return [
            'id' => $request->id,
            'requester_user_id' => $request->requester_user_id,
            'kennel_id' => $request->kennel_id,
            'dog_id' => $request->dog_id,
            'request_type' => $request->request_type,
            'message' => $request->message,
            'status' => $request->status,
            'created_at' => $request->created_at?->toIso8601String(),
            'updated_at' => $request->updated_at?->toIso8601String(),

            // kapcsolatok
            'permission' => $request->permission
                ? AccessPermissionTransformer::item($request->permission)
                : null,
        ];
    }

    public static function collection($requests): array
    {
        return $requests->map(fn($r) => self::item($r))->toArray();
    }
}