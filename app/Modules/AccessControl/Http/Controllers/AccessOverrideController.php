<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Services;

class AccessOverrideController extends Controller
{
    public function __construct(
        protected OverrideService $service
    ) {}

    public function set()
    {
        $userId = request()->get('user_id');
        $kennelId = request()->get('kennel_id');
        $fields = request()->get('allowed_fields', []);

        $override = $this->service->setOverride($userId, $kennelId, $fields);

        return response()->json([
            'success' => true,
            'override' => $override,
        ]);
    }

    public function remove()
    {
        $userId = request()->get('user_id');
        $kennelId = request()->get('kennel_id');

        $this->service->removeOverride($userId, $kennelId);

        return response()->json(['success' => true]);
    }
}