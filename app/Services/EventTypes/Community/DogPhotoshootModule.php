<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class DogPhotoshootModule extends BaseEventType
{
    public string $key = 'dog_photoshoot';
    public string $name = 'Dog Photoshoot';

    public array $fields = [
        'photographer' => ['type' => 'string', 'required' => false],
        'location' => ['type' => 'string', 'required' => true],
        'theme' => ['type' => 'string', 'required' => false],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['photographer','location','theme','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}