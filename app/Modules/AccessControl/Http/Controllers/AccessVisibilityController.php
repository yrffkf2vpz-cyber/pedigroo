<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Services\VisibilityService;

class AccessVisibilityController extends Controller
{
    public function __construct(
        protected VisibilityService $service
    ) {}

    public function canView()
    {
        $userId = request()->get('user_id');
        $kennelId = request()->get('kennel_id');
        $field = request()->get('field');

        if (!$userId || !$kennelId || !$field) {
            return response()->json(['error' => 'Missing parameters'], 422);
        }

        $allowed = $service->canView($userId, $kennelId, $field);

        return response()->json([
            'allowed' => $allowed
        ]);
    }
}