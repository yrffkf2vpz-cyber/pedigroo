<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class WorkingTestModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'working_test';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Working Test';

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
                'beginner',
                'novice',
                'open',
                'elite',
            ],
        ],

        // Feladatok pontozása
        'marking_retrieve_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Marking Retrieve Score',
            'min' => 0,
            'max' => 20,
        ],

        'memory_retrieve_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Memory Retrieve Score',
            'min' => 0,
            'max' => 20,
        ],

        'blind_retrieve_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Blind Retrieve Score',
            'min' => 0,
            'max' => 20,
        ],

        'heelwork_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Heelwork Score',
            'min' => 0,
            'max' => 20,
        ],

        'steadiness_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Steadiness Score',
            'min' => 0,
            'max' => 10,
        ],

        'water_work_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Water Work Score',
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
            'marking_retrieve_score',
            'memory_retrieve_score',
            'blind_retrieve_score',
            'heelwork_score',
            'steadiness_score',
            'water_work_score',
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