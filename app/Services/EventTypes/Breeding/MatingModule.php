<?php

namespace App\Services\EventTypes\Breeding;

use App\Services\EventTypes\BaseEventType;

class MatingModule extends BaseEventType
{
    public string $key = 'mating';
    public string $name = 'Mating';

    public array $fields = [

        'male_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Male Dog ID',
        ],

        'female_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Female Dog ID',
        ],

        'mating_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Mating Type',
            'options' => [
                'Natural',
                'Artificial Insemination (Fresh)',
                'Artificial Insemination (Chilled)',
                'Artificial Insemination (Frozen)',
                'Surgical Insemination',
                'Other',
            ],
        ],

        'location' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Location',
        ],

        'supervised_by' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Supervised By',
        ],

        'successful_tie' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Successful Tie',
        ],

        'tie_duration_minutes' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Tie Duration (minutes)',
            'min' => 1,
            'max' => 60,
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['location', 'supervised_by', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha nincs tie, akkor ne legyen duration
        if (empty($data['successful_tie'])) {
            unset($data['tie_duration_minutes']);
        }

        return $data;
    }

    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}