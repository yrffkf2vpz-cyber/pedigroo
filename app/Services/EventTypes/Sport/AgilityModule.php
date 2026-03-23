<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class AgilityModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'agility';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Agility';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'course_level' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Course Level',
            'options' => [
                'beginner',
                'intermediate',
                'advanced',
                'masters',
            ],
        ],

        'course_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Course Type',
            'options' => [
                'standard',
                'jumping',
                'games',
            ],
        ],

        'time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Run Time (seconds)',
            'min' => 0.01,
        ],

        'faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Faults',
            'min' => 0,
        ],

        'refusals' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Refusals',
            'min' => 0,
        ],

        'eliminated' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Eliminated',
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