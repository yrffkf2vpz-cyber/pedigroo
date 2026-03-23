<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class WaterTestModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'water_test';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Water Test';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'category' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Category',
            'options' => [
                'basic',
                'intermediate',
                'advanced',
            ],
        ],

        // Feladatok pontozása
        'water_entry_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Water Entry Score',
            'min' => 0,
            'max' => 20,
        ],

        'swimming_endurance_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Swimming Endurance Score',
            'min' => 0,
            'max' => 20,
        ],

        'direction_control_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Direction Control Score',
            'min' => 0,
            'max' => 20,
        ],

        'retrieve_from_water_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Retrieve from Water Score',
            'min' => 0,
            'max' => 20,
        ],

        'obedience_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Obedience Score',
            'min' => 0,
            'max' => 20,
        ],

        'total_score' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Total Score',
            'min' => 0,
            'max' => 100,
        ],

        'passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Passed',
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 50,
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Judge Notes',
        ],
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['judge', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Total score automatikus számítása
        $scoreFields = [
            'water_entry_score',
            'swimming_endurance_score',
            'direction_control_score',
            'retrieve_from_water_score',
            'obedience_score',
        ];

        if (!isset($data['total_score'])) {
            $sum = 0;
            foreach ($scoreFields as $field) {
                if (isset($data[$field])) {
                    $sum += $data[$field];
                }
            }
            $data['total_score'] = $sum;
        }

        // Passed státusz (általában 70% felett)
        if (isset($data['total_score']) && !isset($data['passed'])) {
            $data['passed'] = $data['total_score'] >= 70;
        }

        return $data;
    }

    /**
     * Validáció – a BaseEventType kezeli.
     */
    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}