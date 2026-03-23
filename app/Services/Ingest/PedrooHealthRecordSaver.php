<?php

namespace App\Services\Ingest;

use Illuminate\Support\Facades\DB;

class PedrooHealthRecordSaver
{
    public function save($dog, array $health): void
    {
        $dogId = $dog->id; // Pedroo sandbox dog_id

        $items = $health['items'] ?? [];

        foreach ($items as $item) {

            if (!$item['type'] || !$item['value']) {
                continue;
            }

            // Duplikáció ellenőrzés
            $exists = DB::table('pedroo_health_records')
                ->where('dog_id', $dogId)
                ->where('type', $item['type'])
                ->where('value', $item['value'])
                ->where('lab', $item['lab'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('pedroo_health_records')->insert([
                'dog_id'    => $dogId,
                'type'      => $item['type'],   // HD / ED / DM
                'value'     => $item['value'],  // A / 0/0 / CLEAR
                'date'      => $item['date'],   // YYYY-MM-DD vagy null
                'lab'       => $item['lab'],    // BVA / OFA / null
                'source'    => 'pipeline',
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
        }
    }
}