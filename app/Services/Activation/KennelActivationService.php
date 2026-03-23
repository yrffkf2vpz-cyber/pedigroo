<?php

namespace App\Services\Activation;

use App\Models\PdPendingKennels;
use App\Models\PdKennels;
use App\Models\User;
use App\Services\Timeline\KennelTimelineService;
use Illuminate\Support\Facades\DB;

class KennelActivationService
{
    public function __construct(
        protected KennelTimelineService $timeline,
    ) {}

    public function activateByToken(string $token, User $user): ?PdKennels
    {
        $pending = PdPendingKennels::where('activation_token', $token)
            ->where('activation_status', 'pending')
            ->first();

        if (!$pending) {
            return null;
        }

        return DB::transaction(function () use ($pending, $user) {
            $kennel = PdKennels::create([
                'name'                => $pending->name,
                'prefix'              => null,
                'suffix'              => null,
                'registration_number' => null,
                'registry_authority'  => null,
                'country_code'        => $this->normalizeCountry($pending->country),
                'city'                => null,
                'is_active'           => 1,
            ]);

            $pending->update([
                'activation_status' => 'activated',
            ]);

            $this->timeline->kennelActivated($kennel, $user);

            return $kennel;
        });
    }

    protected function normalizeCountry(?string $country): ?string
    {
        if (!$country) return null;

        $key = strtolower(trim($country));
        return match ($key) {
            'hungary', 'hun' => 'HU',
            default          => strtoupper(substr($key, 0, 2)),
        };
    }
}