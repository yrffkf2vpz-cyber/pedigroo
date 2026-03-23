<?php

namespace App\Services\Kennel;

use App\Models\PedrooKennels;
use App\Models\PdPendingKennels;
use App\Models\PdKennels;
use App\Services\Normalizers\NormalizeKennelService;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KennelNormalizerPipelineService
{
    public function __construct(
        protected NormalizeKennelService $normalizer
    ) {}

    /**
     * A teljes pipeline:
     * pedroo_kennels ? pd_pending_kennels / pd_kennels
     */
    public function process(PedrooKennels $raw): PdPendingKennels|PdKennels
    {
        // 1) Normalizálás
        $normalized = $this->normalizer->normalize(
            rawKennel: $raw->name,
            country: $raw->country,
            debug: false
        );

        $canonical = $normalized['kennel_name'];
        $existingId = $normalized['kennel_id'];

        // 2) Ha már létezik végleges kennel ? skip / merge
        if ($existingId) {
            return PdKennels::find($existingId);
        }

        // 3) 15 éves szabály
        $createdAt = Carbon::parse($raw->created_at);
        $ageInYears = $createdAt->diffInYears(now());

        if ($ageInYears >= 15) {
            return $this->finalizeImmediately($canonical, $raw);
        }

        // 4) Fiatalabb mint 15 év ? pending
        return $this->createPending($canonical, $raw);
    }

    /**
     * 15+ év ? azonnal pd_kennels
     */
    private function finalizeImmediately(string $canonical, PedrooKennels $raw): PdKennels
    {
        return PdKennels::create([
            'name'                => $canonical,
            'prefix'              => null,
            'suffix'              => null,
            'registration_number' => null,
            'registry_authority'  => null,
            'country_code'        => $this->normalizeCountry($raw->country),
            'city'                => null,
            'is_active'           => 1,
        ]);
    }

    /**
     * 15 évnél fiatalabb ? pd_pending_kennels
     */
    private function createPending(string $canonical, PedrooKennels $raw): PdPendingKennels
    {
        return PdPendingKennels::create([
            'name'                   => $canonical,
            'country'                => $raw->country,
            'owner_name_raw'         => $raw->owner_name,
            'created_from_pedroo_id' => $raw->id,
            'activation_status'      => 'pending',
            'activation_token'       => Str::random(40),
            'protected_until'        => now()->addYears(15),
        ]);
    }

    private function normalizeCountry(?string $country): ?string
    {
        if (!$country) return null;

        $map = [
            'hungary' => 'HU',
            'hun'     => 'HU',
            'germany' => 'DE',
            'deu'     => 'DE',
            'usa'     => 'US',
            'united states' => 'US',
        ];

        $key = strtolower(trim($country));
        return $map[$key] ?? strtoupper(substr($key, 0, 2));
    }
}