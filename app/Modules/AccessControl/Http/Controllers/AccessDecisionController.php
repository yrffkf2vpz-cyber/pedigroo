<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Http\Requests\ApproveAccessRequest;
use App\Modules\AccessControl\Http\Requests\DenyAccessRequest;
use App\Modules\AccessControl\Repositories\AccessRequestRepository;
use App\Modules\AccessControl\Services\AccessDecisionService;
use App\Modules\AccessControl\Http\Transformers\AccessPermissionTransformer;
use App\Modules\AccessControl\Http\Transformers\AccessRequestTransformer;

class AccessDecisionController extends Controller
{
    public function __construct(
        protected AccessDecisionService $service,
        protected AccessRequestRepository $repo
    ) {}

    public function approve(int $id, ApproveAccessRequest $request)
    {
        $accessRequest = $this->repo->find($id);

        if (!$accessRequest) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $data = $request->validated();

        $permission = $this->service->approve(
            $accessRequest,
            auth()->id(),
            $data['allowed_fields'],
            $data['expires_at'] ?? null
        );

        return AccessPermissionTransformer::item($permission);
    }

    public function deny(int $id, DenyAccessRequest $request)
    {
        $accessRequest = $this->repo->find($id);

        if (!$accessRequest) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $this->service->deny($accessRequest, auth()->id());

        return AccessRequestTransformer::item($accessRequest);
    }
}