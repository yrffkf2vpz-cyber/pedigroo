<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class DogPicnicModule extends BaseEventType
{
    public string $key = 'dog_picnic';
    public string $name = 'Dog Picnic';

    public array $fields = [
        'location' => ['type' => 'string', 'required' => true],
        'organizer' => ['type' => 'string', 'required' => false],
        'food_provided' => ['type' => 'boolean', 'required' => false],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['location','organizer','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}