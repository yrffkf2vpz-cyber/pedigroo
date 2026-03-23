<?php

namespace App\Services\Kennel;

use App\Models\Kennel\Kennel;
use App\Models\Kennel\PendingKennel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DataLifecycleService
{
    /**
     * Pending ? final mozgatás egy adott entitásra.
     *
     * @throws \Throwable
     */
    public function movePendingToFinal(string $entity, int $pendingId, ?User $actor = null): Model
    {
        $map = $this->getEntityMap($entity);

        /** @var class-string<Model> $pendingClass */
        $pendingClass = $map['pending'];
        /** @var class-string<Model> $finalClass */
        $finalClass   = $map['final'];

        return DB::transaction(function () use ($pendingClass, $finalClass, $pendingId, $entity, $actor) {
            /** @var Model|null $pending */
            $pending = $pendingClass::query()->find($pendingId);

            if (!$pending) {
                throw new RuntimeException("Pending {$entity} #{$pendingId} not found");
            }

            if (!$this->validateDataIntegrity($pending, $entity)) {
                throw new RuntimeException("Pending {$entity} #{$pendingId} failed integrity validation");
            }

            // Entitás-specifikus mezo-leképezés
            $data = $this->mapPendingToFinalData($entity, $pending);

            /** @var Model $final */
            $final = $finalClass::query()->create($data);

            // Pending rekord „lezárása”, nem hard delete
            $this->markPendingAsProcessed($pending, $entity, $actor);

            Log::info("Pending {$entity} promoted to final", [
                'entity'      => $entity,
                'pending_id'  => $pendingId,
                'final_id'    => $final->getKey(),
                'actor_id'    => $actor?->id,
            ]);

            return $final;
        });
    }

    /**
     * Sandbox ? pending mozgatás (késobbi bovítéshez elokészítve).
     */
    public function moveSandboxToPending(string $entity, int $sandboxId): Model
    {
        // Jelenleg kennel-fókuszú architektúra – sandbox réteg késobb kerül bevezetésre.
        // A metódus szignatúrája már most része az API-nak, hogy a pipeline-ok rá tudjanak épülni.
        throw new RuntimeException("Sandbox ? pending lifecycle is not implemented yet for entity [{$entity}]");
    }

    /**
     * Adatintegritás ellenorzése entitás szerint.
     */
    public function validateDataIntegrity(Model $record, string $entity): bool
    {
        switch ($entity) {
            case 'kennel':
                /** @var PendingKennel $record */
                if (empty($record->name) || empty($record->owner_id)) {
                    return false;
                }

                // Duplikált kennel név ellenorzés (alap szintu védelem)
                $exists = Kennel::query()
                    ->where('name', $record->name)
                    ->exists();

                return !$exists;

            default:
                // Más entitásokra késobb bovítheto
                return true;
        }
    }

    /**
     * Adat archiválása (nem törlés, hanem állapotváltás / jelölés).
     */
    public function archiveData(string $entity, int $id, string $reason): void
    {
        $map = $this->getEntityMap($entity);
        /** @var class-string<Model> $finalClass */
        $finalClass = $map['final'];

        /** @var Model|null $model */
        $model = $finalClass::query()->find($id);

        if (!$model) {
            throw new RuntimeException("Cannot archive {$entity} #{$id} – not found");
        }

        // Kennel-specifikus archiválás
        if ($entity === 'kennel' && $model instanceof Kennel) {
            $model->update([
                'status'      => 'inactive',
                'archived_at' => now(),
            ]);
        } else {
            // Generikus fallback – bovítheto entitás-specifikus mezokkel
            if ($model->isFillable('status')) {
                $model->setAttribute('status', 'archived');
            }
            if ($model->isFillable('archived_at')) {
                $model->setAttribute('archived_at', now());
            }
            $model->save();
        }

        Log::info("Entity archived", [
            'entity'   => $entity,
            'id'       => $id,
            'reason'   => $reason,
        ]);
    }

    // ===================== Private helpers =====================

    /**
     * Entitás ? pending/final model osztály leképezés.
     */
    private function getEntityMap(string $entity): array
    {
        $map = [
            'kennel' => [
                'pending' => PendingKennel::class,
                'final'   => Kennel::class,
            ],
            // késobb: 'dog', 'owner', stb.
        ];

        if (!isset($map[$entity])) {
            throw new RuntimeException("Unsupported entity for lifecycle operations: [{$entity}]");
        }

        return $map[$entity];
    }

    /**
     * Pending ? final mezoleképezés entitás szerint.
     */
    private function mapPendingToFinalData(string $entity, Model $pending): array
    {
        switch ($entity) {
            case 'kennel':
                /** @var PendingKennel $pending */
                return [
                    'owner_id'           => $pending->owner_id,
                    'name'               => $pending->name,
                    'registration_number'=> $pending->registration_number,
                    'status'             => 'active',
                    'established_date'   => $pending->established_date ?? null,
                    'verified_at'        => now(),
                ];

            default:
                throw new RuntimeException("No pending?final mapping defined for entity [{$entity}]");
        }
    }

    /**
     * Pending rekord lezárása (nem hard delete, hanem állapotváltás + meta).
     */
    private function markPendingAsProcessed(Model $pending, string $entity, ?User $actor = null): void
    {
        if ($pending->isFillable('status')) {
            $pending->setAttribute('status', 'processed');
        }
        if ($pending->isFillable('processed_at')) {
            $pending->setAttribute('processed_at', now());
        }
        if ($pending->isFillable('processed_by')) {
            $pending->setAttribute('processed_by', $actor?->id);
        }

        $pending->save();

        Log::info("Pending {$entity} marked as processed", [
            'entity'     => $entity,
            'pending_id' => $pending->getKey(),
            'actor_id'   => $actor?->id,
        ]);
    }
}