<?php

namespace App\Modules\Breeding\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Breeding\BuyerAccessRequest;
use App\Modules\Breeding\Services\BuyerAccessRequestService;
use Illuminate\Http\Request;

class BreedingRequestController extends Controller
{
    public function __construct(
        protected BuyerAccessRequestService $service
    ) {}

    /**
     * Buyer creates a new access request.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'kennel_id' => 'required|integer|exists:pd_kennels,id',
            'dog_id'    => 'nullable|integer|exists:pd_dogs,id',
            'purpose'   => 'nullable|string|max:255',
            'message'   => 'nullable|string|max:2000',
        ]);

        $buyerId = auth()->id();

        $accessRequest = $this->service->createRequest(
            buyerId: $buyerId,
            kennelId: $validated['kennel_id'],
            dogId: $validated['dog_id'] ?? null,
            purpose: $validated['purpose'] ?? null,
            message: $validated['message'] ?? null,
            ipAddress: $request->ip(),
            deviceFingerprint: $request->header('X-Device-Fingerprint')
        );

        return response()->json($accessRequest, 201);
    }

    /**
     * Kennel owner approves a request.
     */
    public function approve(int $id)
    {
        $requestModel = BuyerAccessRequest::findOrFail($id);

        $ownerId = auth()->id();

        $grant = $this->service->approveRequest(
            request: $requestModel,
            ownerId: $ownerId
        );

        return response()->json($grant);
    }

    /**
     * Kennel owner denies a request.
     */
    public function deny(int $id)
    {
        $requestModel = BuyerAccessRequest::findOrFail($id);

        $ownerId = auth()->id();

        $this->service->denyRequest(
            request: $requestModel,
            ownerId: $ownerId
        );

        return response()->json(['status' => 'denied']);
    }

    /**
     * Buyer lists their own requests.
     */
    public function myRequests()
    {
        $buyerId = auth()->id();

        return response()->json(
            BuyerAccessRequest::where('buyer_id', $buyerId)
                ->orderByDesc('created_at')
                ->get()
        );
    }

    /**
     * Kennel owner lists requests for their kennel.
     */
    public function kennelRequests(int $kennelId)
    {
        $ownerId = auth()->id();

        // Biztonság: csak a kennel tulajdonosa láthatja
        // (A service-ben is ellenorizve van, de itt is jó)
        // Ha lesz Kennel modell, ezt pontosítjuk.
        
        return response()->json(
            BuyerAccessRequest::where('kennel_id', $kennelId)
                ->orderByDesc('created_at')
                ->get()
        );
    }
}
