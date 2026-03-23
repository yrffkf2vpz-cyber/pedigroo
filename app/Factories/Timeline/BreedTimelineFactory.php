<?php

namespace App\Factories\Timeline;

use App\Services\Timeline\BreedTimelineService;

class BreedTimelineFactory
{
    public function __construct(
        protected BreedTimelineService $service
    ) {}

    public function fciAccepted(int $breedId)
    {
        return $this->service->addEvent($breedId, 'fci_accepted');
    }

    public function standardChanged(int $breedId, array $changes)
    {
        return $this->service->addEvent($breedId, 'standard_changed', $changes);
    }

    public function firstAppearance(int $breedId, string $country)
    {
        return $this->service->addEvent($breedId, 'first_appearance_in_country', [
            'country' => $country,
        ]);
    }

    public function majorShowResult(int $breedId, array $result)
    {
        return $this->service->addEvent($breedId, 'major_show_result', $result);
    }
}