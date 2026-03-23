<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DataLifecycleService
{
    public function __construct(
        protected int $yearsThreshold = 15
    ) {}

    /**
     * 1) Import ? mindig pedroo_ (sandbox) táblába.
     */
    public function storeImportedRecord(
        string $entity,          // pl. 'dogs', 'kennels'
        array $rawData,
        ?int $importedBy = null,
        ?string $source = null
    ): void {
        $table = $this->sandboxTable($entity);

        DB::table($table)->insert([
            'original_record' => json_encode($rawData),
            'source'          => $source,
            'imported_by'     => $importedBy,
            'status'          => 'sandbox',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    /**
     * 2) Normalizált adat ? pending_ vagy pd_ a kor alapján.
     *
     * @param  string $entity      pl. 'dogs'
     * @param  array  $normalized  normalizált adat
     * @param  Carbon $reference   pl. születési dátum / esemény dátum
     */
    public function storeNormalizedRecord(
        string $entity,
        array $normalized,
        Carbon $reference
    ): void {
        if ($this->isOlderThanThreshold($reference)) {
            $this->storePublicRecord($entity, $normalized);
        } else {
            $this->storePendingRecord($entity, $normalized);
        }
    }

    /**
     * 3) Felhasználói engedély ? pending_ ? pd_.
     *
     * Csak 15 évnél fiatalabb adatokra értelmezett.
     */
    public function promoteWithUserConsent(
        string $entity,
        int $pendingId,
        int $userId
    ): void {
        $pendingTable = $this->pendingTable($entity);
        $publicTable  = $this->publicTable($entity);

        $pending = DB::table($pendingTable)->where('id', $pendingId)->first();

        if (!$pending) {
            throw new \RuntimeException("Pending record not found for {$entity} #{$pendingId}");
        }

        $data = (array) $pending;

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $data['approved_by'] = $userId;
        $data['approved_at'] = now();

        DB::transaction(function () use ($pendingTable, $publicTable, $pendingId, $data) {
            DB::table($publicTable)->insert($data);
            DB::table($pendingTable)->where('id', $pendingId)->delete();
        });
    }

    /**
     * 4) Törlés ? vissza pedroo_ (sandbox) táblába, pihentetés.
     *
     * Nincs adatvesztés, csak kivesszük a pd_ / pending_ rétegbol.
     */
    public function softDeleteToSandbox(
        string $entity,
        int $id,
        string $layer,          // 'public' | 'pending'
        int $deletedBy,
        ?string $reason = null
    ): void {
        $sourceTable = $layer === 'public'
            ? $this->publicTable($entity)
            : $this->pendingTable($entity);

        $record = DB::table($sourceTable)->where('id', $id)->first();

        if (!$record) {
            throw new \RuntimeException("Record not found for {$entity} #{$id} in {$layer} layer");
        }

        $sandboxTable = $this->sandboxTable($entity);

        DB::transaction(function () use ($sandboxTable, $sourceTable, $id, $record, $deletedBy, $reason) {
            DB::table($sandboxTable)->insert([
                'original_id'     => $record->id,
                'source_table'    => $sourceTable,
                'original_record' => json_encode($record),
                'deleted_by'      => $deletedBy,
                'deleted_reason'  => $reason,
                'deleted_at'      => now(),
                'reactivate_at'   => now()->addYears($this->yearsThreshold),
                'status'          => 'sandbox',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            DB::table($sourceTable)->where('id', $id)->delete();
        });
    }

    /**
     * 5) 15 év után automatikus visszaállítás pedroo_ ? pd_.
     *
     * Ezt egy cron/command hívja idoszakosan.
     */
    public function reactivateDueRecords(string $entity): void
    {
        $sandboxTable = $this->sandboxTable($entity);
        $publicTable  = $this->publicTable($entity);

        $due = DB::table($sandboxTable)
            ->where('status', 'sandbox')
            ->where('reactivate_at', '<=', now())
            ->get();

        foreach ($due as $row) {
            $data = json_decode($row->original_record, true) ?? [];

            unset($data['id'], $data['created_at'], $data['updated_at']);

            DB::transaction(function () use ($sandboxTable, $publicTable, $row, $data) {
                DB::table($publicTable)->insert($data);

                DB::table($sandboxTable)
                    ->where('id', $row->id)
                    ->update([
                        'status'     => 'restored',
                        'updated_at' => now(),
                    ]);
            });
        }
    }

    // -------------------------------------------------------------
    // Segédfüggvények
    // -------------------------------------------------------------

    protected function isOlderThanThreshold(Carbon $reference): bool
    {
        return $reference->lte(now()->subYears($this->yearsThreshold));
    }

    protected function sandboxTable(string $entity): string
    {
        // pl. 'dogs' ? 'pedroo_dogs'
        return 'pedroo_' . $entity;
    }

    protected function pendingTable(string $entity): string
    {
        // pl. 'dogs' ? 'pending_dogs'
        return 'pending_' . $entity;
    }

    protected function publicTable(string $entity): string
    {
        // pl. 'dogs' ? 'pd_dogs'
        return 'pd_' . $entity;
    }

    protected function storePendingRecord(string $entity, array $data): void
    {
        $table = $this->pendingTable($entity);

        DB::table($table)->insert([
            ...$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function storePublicRecord(string $entity, array $data): void
    {
        $table = $this->publicTable($entity);

        DB::table($table)->insert([
            ...$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
