<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class RallyModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'rally';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Rally Obedience';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'level' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Rally Level',
            'options' => [
                'RO1',
                'RO2',
                'RO3',
                'RO4',
            ],
        ],

        'starting_points' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Starting Points',
            'min' => 0,
            'max' => 100,
        ],

        'deductions' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Point Deductions',
            'min' => 0,
            'max' => 100,
        ],

        'final_score' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Final Score',
            'min' => 0,
            'max' => 100,
        ],

        'percentage' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Percentage',
            'min' => 0,
            'max' => 100,
        ],

        'passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Passed',
        ],

        'time' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Course Time (seconds)',
            'min' => 0.01,
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

        // Final score számítása
        if (
            isset($data['starting_points'], $data['deductions'])
            && !isset($data['final_score'])
        ) {
            $data['final_score'] = max(0, $data['starting_points'] - $data['deductions']);
        }

        // Percentage számítása
        if (
            isset($data['final_score'], $data['starting_points'])
            && !isset($data['percentage'])
        ) {
            $data['percentage'] = ($data['final_score'] / $data['starting_points']) * 100;
        }

        // Passed státusz
        if (isset($data['percentage']) && !isset($data['passed'])) {
            $data['passed'] = $data['percentage'] >= 70;
        }

        return $data;
    }

    /**
     * Validáció – a BaseEventType kezeli,
     * de itt adhatsz hozzá extra szabályokat.
     */
    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}