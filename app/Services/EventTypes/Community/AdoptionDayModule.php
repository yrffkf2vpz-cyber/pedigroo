<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class AdoptionDayModule extends BaseEventType
{
    public string $key = 'adoption_day';
    public string $name = 'Adoption Day';

    public array $fields = [
        'shelter' => ['type' => 'string', 'required' => true],
        'location' => ['type' => 'string', 'required' => true],
        'available_dogs' => ['type' => 'integer', 'required' => false, 'min' => 0, 'max' => 500],
        'adopted_dogs' => ['type' => 'integer', 'required' => false, 'min' => 0, 'max' => 500],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['shelter','location','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}