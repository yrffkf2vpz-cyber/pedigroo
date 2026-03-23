<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class FieldTrialModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'field_trial';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Field Trial';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'terrain_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Terrain Type',
            'options' => [
                'open_field',
                'woodland',
                'mixed',
                'water',
            ],
        ],

        'game_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Game Type',
            'options' => [
                'pheasant',
                'partridge',
                'duck',
                'rabbit',
                'mixed',
            ],
        ],

        // Pontozási szekciók
        'search_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Search Score',
            'min' => 0,
            'max' => 20,
        ],

        'style_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Style Score',
            'min' => 0,
            'max' => 20,
        ],

        'steadiness_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Steadiness Score',
            'min' => 0,
            'max' => 20,
        ],

        'retrieve_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Retrieve Score',
            'min' => 0,
            'max' => 20,
        ],

        'response_to_shot_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Response to Shot Score',
            'min' => 0,
            'max' => 10,
        ],

        'obedience_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Obedience Score',
            'min' => 0,
            'max' => 10,
        ],

        'total_score' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Total Score',
            'min' => 0,
            'max' => 100,
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 50,
        ],

        'passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Passed',
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
            'search_score',
            'style_score',
            'steadiness_score',
            'retrieve_score',
            'response_to_shot_score',
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