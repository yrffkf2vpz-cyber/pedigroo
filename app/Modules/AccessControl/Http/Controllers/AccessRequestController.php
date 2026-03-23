<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Http\Requests\CreateAccessRequest;
use App\Modules\AccessControl\Repositories\AccessRequestRepository;
use App\Modules\AccessControl\Services\AccessRequestService;
use App\Modules\AccessControl\Http\Transformers\AccessRequestTransformer;

class AccessRequestController extends Controller
{
    public function __construct(
        protected AccessRequestService $service,
        protected AccessRequestRepository $repo
    ) {}

    public function create(CreateAccessRequest $request)
    {
        $data = $request->validated();

        $accessRequest = $this->service->create(
            auth()->id(),
            $data['kennel_id'],
            $data['dog_id'] ?? null,
            $data['request_type'],
            $data['message'] ?? null
        );

        return AccessRequestTransformer::item($accessRequest);
    }

    public function listForKennel(int $kennelId)
    {
        $requests = $this->repo->forKennel($kennelId);

        return AccessRequestTransformer::collection($requests);
    }

    public function listForUser(int $userId)
    {
        $requests = $this->repo->forUser($userId);

        return AccessRequestTransformer::collection($requests);
    }

    public function detail(int $id)
    {
        $request = $this->repo->find($id);

        if (!$request) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return AccessRequestTransformer::item($request);
    }
}