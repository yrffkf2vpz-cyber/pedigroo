<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class OnlineCompetitionModule extends BaseEventType
{
    public string $key = 'online_competition';
    public string $name = 'Online Competition';

    public array $fields = [
        'competition_name' => ['type' => 'string', 'required' => true],
        'platform' => ['type' => 'string', 'required' => false],
        'result' => ['type' => 'string', 'required' => false],
        'position' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 10000],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['competition_name','platform','result','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}