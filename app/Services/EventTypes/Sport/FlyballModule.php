<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class FlyballModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'flyball';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Flyball';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'division' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Division',
        ],

        'time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Run Time (seconds)',
            'min' => 0.01,
        ],

        'false_starts' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'False Starts',
            'min' => 0,
        ],

        'missed_hurdles' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Missed Hurdles',
            'min' => 0,
        ],

        'ball_drops' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Ball Drops',
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
        foreach (['judge', 'division', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Clean run automatikus meghatározása
        if (
            !isset($data['clean_run']) &&
            isset($data['false_starts'], $data['missed_hurdles'], $data['ball_drops'])
        ) {
            $data['clean_run'] =
                $data['false_starts'] === 0 &&
                $data['missed_hurdles'] === 0 &&
                $data['ball_drops'] === 0;
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