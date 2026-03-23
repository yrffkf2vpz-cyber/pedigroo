<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class DogWorkshopModule extends BaseEventType
{
    public string $key = 'dog_workshop';
    public string $name = 'Dog Workshop';

    public array $fields = [
        'topic' => ['type' => 'string', 'required' => true],
        'instructor' => ['type' => 'string', 'required' => false],
        'location' => ['type' => 'string', 'required' => true],
        'capacity' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 500],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['topic','instructor','location','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}