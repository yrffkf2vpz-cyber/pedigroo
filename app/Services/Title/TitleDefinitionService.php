<?php

namespace App\Services\Title;

use Illuminate\Support\Facades\DB;

class TitleDefinitionService
{
    /**
     * Insert or update a title definition.
     *
     * @param array $data
     * @return int  The ID of the title definition
     */
    public function upsert(array $data): int
    {
        return DB::transaction(function () use ($data) {

            // Kötelező mezők
            if (!isset($data['global_id']) || !isset($data['country_id']) || !isset($data['title_code'])) {
                throw new \Exception("Missing required fields: global_id, country_id, title_code");
            }

            // Duplikáció ellenőrzés
            $existing = DB::table('title_definitions')
                ->where('global_id', $data['global_id'])
                ->where('country_id', $data['country_id'])
                ->where('title_code', $data['title_code'])
                ->value('id');

            if ($existing) {

                DB::table('title_definitions')
                    ->where('id', $existing)
                    ->update([
                        'title_name' => $data['title_name'] ?? null,
                        'requirement' => $data['requirement'] ?? null,
                        'updated_at' => now(),
                    ]);

                return $existing;
            }

            // Insert
            return DB::table('title_definitions')->insertGetId([
                'global_id'   => $data['global_id'],
                'country_id'  => $data['country_id'],
                'title_code'  => $data['title_code'],
                'title_name'  => $data['title_name'] ?? null,
                'requirement' => $data['requirement'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });
    }

    /**
     * Find a title definition by canonical keys.
     */
    public function find(int $globalId, int $countryId, string $titleCode): ?object
    {
        return DB::table('title_definitions')
            ->where('global_id', $globalId)
            ->where('country_id', $countryId)
            ->where('title_code', $titleCode)
            ->first();
    }

    /**
     * List all titles for a country.
     */
    public function listByCountry(int $countryId): array
    {
        return DB::table('title_definitions')
            ->where('country_id', $countryId)
            ->orderBy('title_code')
            ->orderBy('title_name')
            ->get()
            ->toArray();
    }
}