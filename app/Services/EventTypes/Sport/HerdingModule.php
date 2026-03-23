<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class HerdingModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'herding';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Herding';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'stock_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Stock Type',
            'options' => [
                'sheep',
                'cattle',
                'ducks',
                'geese',
            ],
        ],

        'level' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Herding Level',
            'options' => [
                'HT',   // Herding Test
                'PT',   // Pre-Trial
                'HS',   // Started
                'HI',   // Intermediate
                'HX',   // Advanced
            ],
        ],

        // Pontozási szekciók
        'outrun_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Outrun Score',
            'min' => 0,
            'max' => 20,
        ],

        'lift_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Lift Score',
            'min' => 0,
            'max' => 10,
        ],

        'fetch_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Fetch Score',
            'min' => 0,
            'max' => 20,
        ],

        'drive_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Drive Score',
            'min' => 0,
            'max' => 20,
        ],

        'shed_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Shed Score',
            'min' => 0,
            'max' => 10,
        ],

        'pen_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Pen Score',
            'min' => 0,
            'max' => 10,
        ],

        'overall_impression' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Overall Impression',
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
            'outrun_score',
            'lift_score',
            'fetch_score',
            'drive_score',
            'shed_score',
            'pen_score',
            'overall_impression',
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