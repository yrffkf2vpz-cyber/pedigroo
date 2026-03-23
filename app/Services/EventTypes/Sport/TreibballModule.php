<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class TreibballModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'treibball';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Treibball';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'class' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Class',
            'options' => [
                'starter',
                'beginner',
                'intermediate',
                'advanced',
                'expert',
            ],
        ],

        'balls_completed' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Balls Successfully Driven',
            'min' => 0,
            'max' => 8,
        ],

        'total_balls' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Total Balls in Course',
            'min' => 1,
            'max' => 8,
        ],

        'time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Completion Time (seconds)',
            'min' => 0.01,
        ],

        'faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Faults',
            'min' => 0,
        ],

        'direction_control_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Direction Control Score',
            'min' => 0,
            'max' => 20,
        ],

        'teamwork_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Teamwork Score',
            'min' => 0,
            'max' => 20,
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
            'max' => 50,
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
            'direction_control_score',
            'teamwork_score',
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
            $data['passed'] = $data['total_score'] >= 35; // 50 pontból 70%
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