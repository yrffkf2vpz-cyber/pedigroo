<?php

namespace App\Modules\Breeding\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Breeding\BuyerAccessGrant;
use App\Modules\Breeding\Services\BuyerAccessGrantService;
use Illuminate\Http\Request;

class BreedingGrantController extends Controller
{
    public function __construct(
        protected BuyerAccessGrantService $service
    ) {}

    /**
     * Kennel owner revokes a grant manually.
     */
    public function revoke(int $id)
    {
        $grant = BuyerAccessGrant::findOrFail($id);

        $ownerId = auth()->id();

        $this->service->revokeGrant($grant, $ownerId);

        return response()->json(['status' => 'revoked']);
    }

    /**
     * Expire a grant (optional manual trigger).
     */
    public function expire(int $id)
    {
        $grant = BuyerAccessGrant::findOrFail($id);

        $this->service->expireGrant($grant);

        return response()->json(['status' => 'expired']);
    }
}
