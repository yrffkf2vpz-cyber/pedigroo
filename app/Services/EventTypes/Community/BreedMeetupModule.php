<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class BreedMeetupModule extends BaseEventType
{
    public string $key = 'breed_meetup';
    public string $name = 'Breed Meetup';

    public array $fields = [
        'breed' => ['type' => 'string', 'required' => true],
        'location' => ['type' => 'string', 'required' => true],
        'organizer' => ['type' => 'string', 'required' => false],
        'expected_dogs' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 500],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['breed','location','organizer','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}