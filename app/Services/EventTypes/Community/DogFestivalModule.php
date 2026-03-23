<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class DogFestivalModule extends BaseEventType
{
    public string $key = 'dog_festival';
    public string $name = 'Dog Festival';

    public array $fields = [
        'festival_name' => ['type' => 'string', 'required' => true],
        'location' => ['type' => 'string', 'required' => true],
        'organizer' => ['type' => 'string', 'required' => false],
        'expected_visitors' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 100000],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['festival_name','location','organizer','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}