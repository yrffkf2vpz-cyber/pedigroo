<?php

namespace App\Pedroo\Normalize;

use App\Models\Dog;
use App\Models\DogTimelineEvent;

class DogsPipelineStep
{
    protected DogsService $service;

    public function __construct()
    {
        $this->service = new DogsService();
    }

    /**
     * A pipeline ezt hívja meg.
     * A $rows egy tömb, amely minden kutyára tartalmaz egy nyers rekordot.
     */
    public function handle(array $rows): array
    {
        $created = 0;
        $updated = 0;
        $timelineEvents = 0;

        foreach ($rows as $row) {

            // 1) Normalizálás + timeline események generálása
            $result = $this->service->handle($row);

            $dogData = $result['dog'];
            $events  = $result['timeline'];

            // 2) Kutya mentése (updateOrCreate)
            $dog = Dog::updateOrCreate(
                ['reg_no' => $dogData['reg_no']], // egyediség alapja
                $dogData
            );

            if ($dog->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            // 3) Timeline események mentése
            foreach ($events as $event) {
                DogTimelineEvent::create([
                    'dog_id'     => $dog->id,
                    'date'       => $event['date'],
                    'type'       => $event['type'],
                    'title'      => $event['title'],
                    'description'=> $event['description'],
                ]);
                $timelineEvents++;
            }
        }

        // 4) Statisztika visszaadása a pipeline-nak
        return [
            'created'         => $created,
            'updated'         => $updated,
            'timeline_events' => $timelineEvents,
        ];
    }
}
