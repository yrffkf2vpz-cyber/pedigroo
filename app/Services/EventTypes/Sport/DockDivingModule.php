<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class DockDivingModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'dock_diving';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Dock Diving';

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
            'type' => 'enum',
            'required' => true,
            'label' => 'Division',
            'options' => [
                'distance_jump',
                'air_retrieve',
                'speed_retrieve',
            ],
        ],

        'distance' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Jump Distance (meters)',
            'min' => 0.01,
        ],

        'height' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Jump Height (meters)',
            'min' => 0.01,
        ],

        'speed_time' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Speed Retrieve Time (seconds)',
            'min' => 0.01,
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

        // Division-specifikus mezok tisztítása
        if (isset($data['division'])) {
            switch ($data['division']) {
                case 'distance_jump':
                    unset($data['height'], $data['speed_time']);
                    break;

                case 'air_retrieve':
                    unset($data['distance'], $data['speed_time']);
                    break;

                case 'speed_retrieve':
                    unset($data['distance'], $data['height']);
                    break;
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