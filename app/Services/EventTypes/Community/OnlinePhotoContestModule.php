<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class OnlinePhotoContestModule extends BaseEventType
{
    public string $key = 'online_photo_contest';
    public string $name = 'Online Photo Contest';

    public array $fields = [
        'contest_name' => ['type' => 'string', 'required' => true],
        'platform' => ['type' => 'string', 'required' => false],
        'placement' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 10000],
        'votes_received' => ['type' => 'integer', 'required' => false, 'min' => 0, 'max' => 10000000],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['contest_name','platform','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}