<?php

namespace App\Services\Breeding;

use App\Models\Breeding\BuyerAccessRequest;
use App\Models\Breeding\BuyerAccessGrant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyerAccessService
{
    /**
     * Buyer access request létrehozása.
     */
    public function createRequest(array $data): BuyerAccessRequest
    {
        return DB::transaction(function () use ($data) {

            $request = BuyerAccessRequest::create([
                'buyer_id'           => $data['buyer_id'],
                'dog_id'             => $data['dog_id'],
                'kennel_id'          => $data['kennel_id'],
                'purpose'            => $data['purpose'] ?? null,
                'message'            => $data['message'] ?? null,
                'status'             => 'pending',
                'ip_address'         => $data['ip'] ?? request()->ip(),
                'device_fingerprint' => $data['device'] ?? null,
            ]);

            Log::info('Buyer access request created', [
                'request_id' => $request->id,
                'buyer_id'   => $request->buyer_id,
                'dog_id'     => $request->dog_id,
            ]);

            return $request;
        });
    }

    /**
     * Kennel owner döntése: approve / reject.
     */
    public function decide(BuyerAccessRequest $request, string $decision, ?string $note = null): BuyerAccessRequest
    {
        return DB::transaction(function () use ($request, $decision, $note) {

            $request->update([
                'status' => $decision,
            ]);

            Log::info('Buyer access request decision', [
                'request_id' => $request->id,
                'decision'   => $decision,
                'kennel_id'  => $request->kennel_id,
            ]);

            if ($decision === 'approved') {
                $this->createGrant($request);
            }

            return $request;
        });
    }

    /**
     * Grant létrehozása (ha a kennel owner jóváhagyta).
     */
    protected function createGrant(BuyerAccessRequest $request): BuyerAccessGrant
    {
        $grant = BuyerAccessGrant::create([
            'request_id' => $request->id,
            'buyer_id'   => $request->buyer_id,
            'dog_id'     => $request->dog_id,
            'kennel_id'  => $request->kennel_id,
            'expires_at' => now()->addDays(30), // opcionális
        ]);

        Log::info('Buyer access grant created', [
            'grant_id'   => $grant->id,
            'buyer_id'   => $grant->buyer_id,
            'dog_id'     => $grant->dog_id,
        ]);

        return $grant;
    }
}