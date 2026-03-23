<?php

namespace App\Pedroo\Normalize;

use App\Models\BreedTimelineEvent;

class BreedTimelineStep
{
    /**
     * A pipeline ezt hívja meg.
     * A $breedMeta egy tömb, amely tartalmazza a fajta történeti adatait.
     *
     * Példa:
     * [
     *     'breed_id' => 1,
     *     'events' => [
     *         [
     *             'date' => '1900-01-01',
     *             'type' => 'registry_change',
     *             'title' => 'OMKT prefix introduced',
     *             'description' => 'The earliest known Hungarian registry prefix.'
     *         ],
     *         [
     *             'date' => '1960-01-01',
     *             'type' => 'registry_change',
     *             'title' => 'MEOE prefix introduced',
     *             'description' => 'Modernization of the Hungarian registry system.'
     *         ],
     *         [
     *             'date' => '1990-01-01',
     *             'type' => 'registry_change',
     *             'title' => 'MET prefix introduced',
     *             'description' => 'Current Hungarian registry prefix.'
     *         ],
     *     ]
     * ]
     */
    public function handle(array $breedMeta): array
    {
        $breedId = $breedMeta['breed_id'];
        $events  = $breedMeta['events'];

        $created = 0;
        $skipped = 0;

        foreach ($events as $event) {

            // Idempotens mentés: ha már létezik, nem duplikáljuk
            $exists = BreedTimelineEvent::where('breed_id', $breedId)
                ->where('date', $event['date'])
                ->where('type', $event['type'])
                ->where('title', $event['title'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            BreedTimelineEvent::create([
                'breed_id'    => $breedId,
                'date'        => $event['date'],
                'type'        => $event['type'],
                'title'       => $event['title'],
                'description' => $event['description'] ?? null,
            ]);

            $created++;
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
        ];
    }
}
