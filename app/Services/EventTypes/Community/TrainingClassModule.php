<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class TrainingClassModule extends BaseEventType
{
    public string $key = 'training_class';
    public string $name = 'Training Class';

    public array $fields = [
        'class_name' => ['type' => 'string', 'required' => true],
        'trainer' => ['type' => 'string', 'required' => false],
        'location' => ['type' => 'string', 'required' => true],
        'difficulty' => ['type' => 'enum', 'required' => false, 'options' => ['Beginner','Intermediate','Advanced']],
        'capacity' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 200],
        'notes' => ['type' => 'text', 'required' => false],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['class_name','trainer','location','notes'] as $f)
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        return $data;
    }
}