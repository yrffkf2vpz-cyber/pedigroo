<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class DogMeetupModule extends BaseEventType
{
    public string $key = 'dog_meetup';
    public string $name = 'Dog Meetup';

    public array $fields = [

        'location' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Location',
        ],

        'organizer' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Organizer',
        ],

        'expected_dogs' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Expected Number of Dogs',
            'min' => 1,
            'max' => 500,
        ],

        'theme' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Theme (optional)',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['location', 'organizer', 'theme', 'notes'] as $field) {
            if (isset($data[$field])) $data[$field] = trim($data[$field]);
        }
        return $data;
    }
}
