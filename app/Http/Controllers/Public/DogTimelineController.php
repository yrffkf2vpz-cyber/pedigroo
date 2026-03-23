<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Dog;
use App\Models\DogTimelineEvent;
use App\Models\BreedTimelineEvent;

class DogTimelineController extends Controller
{
    /**
     * GET /api/breeds/{breed_id}/timeline
     * Fajtaszintu timeline események
     */
    public function breed($breedId)
    {
        $events = BreedTimelineEvent::where('breed_id', $breedId)
            ->orderBy('date')
            ->get()
            ->map(function ($event) {
                return [
                    'scope'       => 'breed',
                    'type'        => $event->type,
                    'date'        => $event->date,
                    'title'       => $event->title,
                    'description' => $event->description,
                ];
            });

        return response()->json($events);
    }

    /**
     * GET /api/dogs/{dog_id}/timeline
     * Egyedi kutya timeline események
     */
    public function dog($dogId)
    {
        $events = DogTimelineEvent::where('dog_id', $dogId)
            ->orderBy('date')
            ->get()
            ->map(function ($event) {
                return [
                    'scope'       => 'dog',
                    'type'        => $event->type,
                    'date'        => $event->date,
                    'title'       => $event->title,
                    'description' => $event->description,
                ];
            });

        return response()->json($events);
    }

    /**
     * GET /api/dogs/{dog_id}/timeline/merged
     * Összevont timeline (fajta + kutya)
     */
    public function merged($dogId)
    {
        $dog = Dog::findOrFail($dogId);

        // 1) Kutya timeline
        $dogEvents = DogTimelineEvent::where('dog_id', $dogId)
            ->get()
            ->map(function ($event) {
                return [
                    'scope'       => 'dog',
                    'type'        => $event->type,
                    'date'        => $event->date,
                    'title'       => $event->title,
                    'description' => $event->description,
                ];
            })
            ->toArray();

        // 2) Fajta timeline
        $breedEvents = BreedTimelineEvent::where('breed_id', $dog->breed_id)
            ->get()
            ->map(function ($event) {
                return [
                    'scope'       => 'breed',
                    'type'        => $event->type,
                    'date'        => $event->date,
                    'title'       => $event->title,
                    'description' => $event->description,
                ];
            })
            ->toArray();

        // 3) Összefésülés
        $merged = array_merge($breedEvents, $dogEvents);

        // 4) Időrend
        usort($merged, fn($a, $b) => strcmp($a['date'], $b['date']));

        return response()->json($merged);
    }
}
