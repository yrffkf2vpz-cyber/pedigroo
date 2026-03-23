<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class ChampionshipImportService
{
    /**
     * Import a list of championship records into the sandbox table.
     *
     * @param array $rows
     * @return array
     */
    public function import(array $rows): array
    {
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {

            try {
                // 1) Hash generálás a duplikációk kiszűrésére
                $hash = $this->generateHash($row);

                // 2) Duplikáció ellenőrzés sandboxban
                $exists = DB::table('pedroo_championships')
                    ->where('hash', $hash)
                    ->value('id');

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // 3) Insert sandboxba
                DB::table('pedroo_championships')->insert([
                    'dog_name'            => $row['dog_name'] ?? null,
                    'dog_id'              => $row['dog_id'] ?? null,

                    'event_name'          => $row['event_name'] ?? null,
                    'event_id'            => $row['event_id'] ?? null,

                    'title_code'          => $row['title_code'] ?? null,
                    'title_name'          => $row['title_name'] ?? null,
                    'title_definition_id' => $row['title_definition_id'] ?? null,

                    'country'             => $row['country'] ?? null,
                    'date'                => $row['date'] ?? null,

                    'source'              => $row['source'] ?? null,
                    'external_id'         => $row['external_id'] ?? null,

                    'raw'                 => json_encode($row, JSON_UNESCAPED_UNICODE),
                    'hash'                => $hash,
                    'confidence'          => $row['confidence'] ?? 100,

                    'status'              => 'pending',
                    'notes'               => null,

                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);

                $inserted++;

            } catch (\Throwable $e) {
                $errors[] = [
                    'row'   => $index,
                    'data'  => $row,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ];
    }

    /**
     * Generate a stable hash for deduplication.
     */
    protected function generateHash(array $row): string
    {
        return hash('sha256', json_encode([
            $row['dog_name']   ?? null,
            $row['event_name'] ?? null,
            $row['title_code'] ?? null,
            $row['country']    ?? null,
            $row['date']       ?? null,
        ]));
    }
}