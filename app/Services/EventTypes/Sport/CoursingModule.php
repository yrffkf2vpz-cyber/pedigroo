<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class CoursingModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'coursing';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Coursing';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'course_length' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Course Length (meters)',
            'min' => 100,
            'max' => 2000,
        ],

        'time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Run Time (seconds)',
            'min' => 0.01,
        ],

        'speed' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Speed (m/s)',
            'min' => 0.01,
        ],

        'performance_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Performance Score',
            'min' => 0,
            'max' => 100,
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

        // Automatikus sebességszámítás
        if (
            isset($data['course_length'], $data['time'])
            && !isset($data['speed'])
        ) {
            $data['speed'] = $data['course_length'] / $data['time'];
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