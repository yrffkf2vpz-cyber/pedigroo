<?php

namespace App\Services\EventTypes\Breeding;

use App\Services\EventTypes\BaseEventType;

class BirthModule extends BaseEventType
{
    public string $key = 'birth';
    public string $name = 'Birth';

    public array $fields = [

        'female_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Mother (Female Dog ID)',
        ],

        'litter_identifier' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Litter Identifier (optional)',
        ],

        'birth_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Birth Type',
            'options' => [
                'Natural',
                'Assisted',
                'C-Section',
                'Emergency C-Section',
                'Other',
            ],
        ],

        'total_puppies' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Total Puppies Born',
            'min' => 1,
            'max' => 25,
        ],

        'live_puppies' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Live Puppies',
            'min' => 0,
            'max' => 25,
        ],

        'stillborn_puppies' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Stillborn Puppies',
            'min' => 0,
            'max' => 25,
        ],

        'placentas_count' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Number of Placentas',
            'min' => 0,
            'max' => 25,
        ],

        'complications' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Complications (if any)',
        ],

        'assisted_by' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Assisted By (person or veterinarian)',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name (if applicable)',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Additional Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['litter_identifier', 'complications', 'assisted_by', 'clinic', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha nincs stillborn megadva ? számoljuk ki automatikusan
        if (!isset($data['stillborn_puppies']) && isset($data['total_puppies'], $data['live_puppies'])) {
            $data['stillborn_puppies'] = max(0, $data['total_puppies'] - $data['live_puppies']);
        }

        // Placenták száma gyakran = total_puppies, ha nincs megadva
        if (!isset($data['placentas_count']) && isset($data['total_puppies'])) {
            $data['placentas_count'] = $data['total_puppies'];
        }

        return $data;
    }

    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}