<?php

namespace App\Services\EventTypes\Breeding;

use App\Services\EventTypes\BaseEventType;

class LitterCheckModule extends BaseEventType
{
    public string $key = 'litter_check';
    public string $name = 'Litter Check';

    public array $fields = [

        'litter_identifier' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Litter Identifier',
        ],

        'female_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Mother (Female Dog ID)',
        ],

        'total_puppies' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Total Puppies',
            'min' => 1,
            'max' => 25,
        ],

        'puppies_alive' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Puppies Alive',
            'min' => 0,
            'max' => 25,
        ],

        'puppies_deceased' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Puppies Deceased',
            'min' => 0,
            'max' => 25,
        ],

        'average_weight' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Average Puppy Weight (g)',
            'min' => 50,
            'max' => 2000,
        ],

        'health_status' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Overall Litter Health',
            'options' => [
                'Excellent',
                'Good',
                'Fair',
                'Poor',
                'Critical',
            ],
        ],

        'mother_condition' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Mother Condition',
            'options' => [
                'Excellent',
                'Good',
                'Fair',
                'Poor',
                'Critical',
            ],
        ],

        'milk_production' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Milk Production',
            'options' => [
                'Normal',
                'Low',
                'High',
                'None',
            ],
        ],

        'congenital_issues' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Congenital Issues (if any)',
        ],

        'checked_by' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Checked By (vet or breeder)',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Additional Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['litter_identifier', 'congenital_issues', 'checked_by', 'clinic', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha nincs deceased megadva ? automatikusan számoljuk
        if (!isset($data['puppies_deceased']) &&
            isset($data['total_puppies'], $data['puppies_alive'])) {

            $data['puppies_deceased'] = max(0, $data['total_puppies'] - $data['puppies_alive']);
        }

        return $data;
    }

    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}