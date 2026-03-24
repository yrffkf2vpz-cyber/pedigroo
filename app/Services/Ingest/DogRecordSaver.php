<?php

namespace App\Services\Ingest;

use App\Models\Dog;
use App\Models\HealthRecord;

class DogRecordSaver
{
    /**
     * A NormalizePipelineService 3.0 által előállított canonical struktúra mentése.
     */
    public function save(array $normalized): Dog
    {
        $dogData   = $normalized['dog']     ?? [];
        $parents   = $normalized['parents'] ?? [];
        $health    = $normalized['health']  ?? [];
        $history   = $normalized['history'] ?? [];
        $meta      = $normalized['meta']    ?? [];

        // ---------------------------------------------------------
        // 1) DOG LOAD / CREATE (reg_no_clean a primary key)
        // ---------------------------------------------------------
        $dog = Dog::firstOrNew([
            'reg_no_clean' => $dogData['reg_no_clean'] ?? null,
        ]);

        // ---------------------------------------------------------
        // 2) CANONICAL NAME STRUCTURE
        // ---------------------------------------------------------
        $dog->name       = $dogData['name']       ?? null;
        $dog->prefix     = $dogData['prefix']     ?? null;
        $dog->lastname   = $dogData['lastname']   ?? null;
        $dog->firstname  = $dogData['firstname']  ?? null;
        $dog->kennel_id  = $dogData['kennel_id']  ?? null;

        // ---------------------------------------------------------
        // 3) REGISTRATION DATA (canonical)
        // ---------------------------------------------------------
        $dog->reg_no         = $dogData['reg_no']        ?? null;
        $dog->reg_no_clean   = $dogData['reg_no_clean']  ?? null;
        $dog->reg_prefix     = $dogData['reg_prefix']    ?? null;
        $dog->reg_number     = $dogData['reg_number']    ?? null;
        $dog->reg_year       = $dogData['reg_year']      ?? null;
        $dog->reg_country    = $dogData['reg_country']   ?? null;
        $dog->reg_issuer     = $dogData['reg_issuer']    ?? null;

        // ---------------------------------------------------------
        // 4) COLOR (canonical + official + birth)
        // ---------------------------------------------------------
        $dog->color          = $dogData['color']          ?? null;
        $dog->official_color = $dogData['official_color'] ?? null;
        $dog->birth_color    = $dogData['birth_color']    ?? null;

        // ---------------------------------------------------------
        // 5) COUNTRY + BREED
        // ---------------------------------------------------------
        $dog->origin_country = $dogData['country']     ?? null;
        $dog->country_id     = $dogData['country_id']  ?? null;
        $dog->breed_id       = $dogData['breed_id']    ?? null;

        // ---------------------------------------------------------
        // 6) PARENTS (canonical parent_id)
        // ---------------------------------------------------------
        $dog->father_id = $parents['sire']['parent_id'] ?? null;
        $dog->mother_id = $parents['dam']['parent_id']  ?? null;

        // ---------------------------------------------------------
        // 7) HISTORY / ERA CLASSIFICATION
        // ---------------------------------------------------------
        $dog->history_classification = $history['history_classification'] ?? null;

        // ---------------------------------------------------------
        // 8) META (confidence, flags, warnings)
        // ---------------------------------------------------------
        $dog->ai_used    = $meta['ai_used']    ?? true;
        $dog->confidence = $meta['confidence'] ?? 0;
        $dog->flags      = $meta['flags']      ?? null;
        $dog->warnings   = $meta['warnings']   ?? null;

        $dog->save();

        // ---------------------------------------------------------
        // 9) HEALTH RECORDS (3.0 canonical)
        // ---------------------------------------------------------
        if (!empty($health['items'])) {
            foreach ($health['items'] as $item) {
                HealthRecord::updateOrCreate(
                    [
                        'dog_id' => $dog->id,
                        'type'   => $item['type'],
                        'value'  => $item['value'],
                    ],
                    [
                        'lab'  => $item['lab'],
                        'date' => $item['date'],
                        'notes'=> $item['notes'] ?? null,
                    ]
                );
            }
        }

        return $dog;
    }
}