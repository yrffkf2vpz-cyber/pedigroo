<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class HoopersModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'hoopers';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Hoopers';

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

        'time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Run Time (seconds)',
            'min' => 0.01,
        ],

        'time_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Time Faults',
            'min' => 0,
        ],

        'line_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Line Faults',
            'min' => 0,
        ],

        'off_course_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Off-Course Faults',
            'min' => 0,
        ],

        'total_faults' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Total Faults',
            'min' => 0,
        ],

        'clean_run' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Clean Run',
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 100,
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

        // Total faults automatikus számítása
        if (
            !isset($data['total_faults']) &&
            isset($data['time_faults'], $data['line_faults'], $data['off_course_faults'])
        ) {
            $data['total_faults'] =
                $data['time_faults'] +
                $data['line_faults'] +
                $data['off_course_faults'];
        }

        // Clean run automatikusan
        if (!isset($data['clean_run']) && isset($data['total_faults'])) {
            $data['clean_run'] = $data['total_faults'] === 0;
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