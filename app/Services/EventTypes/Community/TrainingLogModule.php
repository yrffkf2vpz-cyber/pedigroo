<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class TrainingLogModule extends BaseEventType
{
    public string $key = 'training_log';
    public string $name = 'Training Log';

    public array $fields = [
        'trainer' => ['type' => 'string', 'required' => false],
        'location' => ['type' => 'string', 'required' => false],
        'focus_area' => ['type' => 'string', 'required' => true],
        'duration_minutes' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 300],
        'progress_notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['trainer','location','focus_area','progress_notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}