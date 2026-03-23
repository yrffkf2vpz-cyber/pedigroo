<?php

namespace App\Services\Owners;

use App\Models\PdOwner;
use App\Models\Kennels;
use App\Models\PdDogOwner;

class NormalizeOwnerEngine
{
    /**
     * A NormalizePipelineService + NormalizeDogService által eloállított
     * canonical, ID-alapú normalizált kutyaadatokból
     * elkészíti a pd_dog_owners pivot rekordokat.
     *
     * @param array $normalizedDog [
     *   'owner_id'   => ?int,
     *   'breeder_id' => ?int,
     *   'kennel_id'  => ?int,
     *   'debug'      => [...],
     *   ...
     * ]
     *
     * @return array [
     *   'owners' => [
     *      ['owner_id' => ..., 'ownership_type' => ..., 'can_edit' => ..., ...],
     *      ...
     *   ]
     * ]
     */
    public function buildOwnershipStructure(array $normalizedDog): array
    {
        $owners = [];

        $ownerId   = $normalizedDog['owner_id']   ?? null;
        $breederId = $normalizedDog['breeder_id'] ?? null;
        $kennelId  = $normalizedDog['kennel_id']  ?? null;

        // --- 1) PRIMARY OWNER LOGIKA ---

        if ($ownerId) {
            // explicit owner ? primary
            $owners[] = $this->pivot(
                ownerId: $ownerId,
                type: 'primary',
                canEdit: true,
                acquiredAt: $normalizedDog['dob'] ?? null
            );
        } elseif ($kennelId) {
            // kennel owner ? primary
            $kennelOwner = $this->resolveKennelOwner($kennelId);
            if ($kennelOwner) {
                $owners[] = $this->pivot(
                    ownerId: $kennelOwner->id,
                    type: 'primary',
                    canEdit: true,
                    acquiredAt: $normalizedDog['dob'] ?? null
                );
            }
        } elseif ($breederId) {
            // breeder ? primary (fallback)
            $owners[] = $this->pivot(
                ownerId: $breederId,
                type: 'primary',
                canEdit: true,
                acquiredAt: $normalizedDog['dob'] ?? null
            );
        }

        // --- 2) BREEDER AS CO-OWNER (opcionális) ---

        if ($breederId && $breederId !== $ownerId) {
            $owners[] = $this->pivot(
                ownerId: $breederId,
                type: 'breeder',
                canEdit: false,
                acquiredAt: $normalizedDog['dob'] ?? null
            );
        }

        // --- 3) KENNEL OWNER AS CO-OWNER (opcionális) ---

        if ($kennelId) {
            $kennelOwner = $this->resolveKennelOwner($kennelId);
            if ($kennelOwner && $kennelOwner->id !== $ownerId) {
                $owners[] = $this->pivot(
                    ownerId: $kennelOwner->id,
                    type: 'kennel-owner',
                    canEdit: false,
                    acquiredAt: $normalizedDog['dob'] ?? null
                );
            }
        }

        // --- 4) CO-OWNERS (NormalizeOwnerService által normalizált lista) ---

        if (!empty($normalizedDog['co_owners'])) {
            foreach ($normalizedDog['co_owners'] as $coOwnerId) {
                $owners[] = $this->pivot(
                    ownerId: $coOwnerId,
                    type: 'co-owner',
                    canEdit: $normalizedDog['co_owner_can_edit'] ?? false,
                    acquiredAt: $normalizedDog['co_owner_since'] ?? null
                );
            }
        }

        // --- 5) HOLDER (aki tartja, de nem tulajdonos) ---

        if (!empty($normalizedDog['holder_id'])) {
            $owners[] = $this->pivot(
                ownerId: $normalizedDog['holder_id'],
                type: 'holder',
                canEdit: false,
                acquiredAt: $normalizedDog['holder_since'] ?? null
            );
        }

        return [
            'owners' => $owners,
        ];
    }

    /**
     * Pivot rekord generálása.
     */
    private function pivot(
        int $ownerId,
        string $type,
        bool $canEdit,
        ?string $acquiredAt = null,
        ?string $releasedAt = null
    ): array {
        return [
            'owner_id'       => $ownerId,
            'ownership_type' => $type,
            'can_edit'       => $canEdit,
            'acquired_at'    => $acquiredAt,
            'released_at'    => $releasedAt,
        ];
    }

    /**
     * Kennel owner feloldása.
     */
    private function resolveKennelOwner(int $kennelId): ?PdOwner
    {
        $kennel = Kennels::find($kennelId);

        if (!$kennel || !$kennel->owner_name) {
            return null;
        }

        return PdOwner::firstOrCreate(
            ['name' => $kennel->owner_name],
            ['country' => $kennel->country ?? null]
        );
    }
}