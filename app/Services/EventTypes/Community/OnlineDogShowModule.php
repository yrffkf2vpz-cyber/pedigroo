<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class OnlineDogShowModule extends BaseEventType
{
    public string $key = 'online_dog_show';
    public string $name = 'Online Dog Show';

    public array $fields = [
        'show_name' => ['type' => 'string', 'required' => true],
        'platform' => ['type' => 'string', 'required' => false],
        'class_entered' => ['type' => 'string', 'required' => false],
        'placement' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 10000],
        'judge' => ['type' => 'string', 'required' => false],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['show_name','platform','class_entered','judge','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}