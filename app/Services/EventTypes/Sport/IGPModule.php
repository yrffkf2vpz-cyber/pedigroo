<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class IGPModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'igp';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'IGP';

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
            'label' => 'IGP Level',
            'options' => [
                'IGP1',
                'IGP2',
                'IGP3',
            ],
        ],

        // A – Tracking
        'tracking_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Tracking Score',
            'min' => 0,
            'max' => 100,
        ],

        // B – Obedience
        'obedience_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Obedience Score',
            'min' => 0,
            'max' => 100,
        ],

        // C – Protection
        'protection_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Protection Score',
            'min' => 0,
            'max' => 100,
        ],

        'total_score' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Total Score',
            'min' => 0,
            'max' => 300,
        ],

        'passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Passed',
        ],

        'helper_name' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Protection Helper Name',
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
        foreach (['judge', 'helper_name', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Automatikus total_score számítás, ha nincs megadva
        if (
            isset($data['tracking_score'], $data['obedience_score'], $data['protection_score'])
            && !isset($data['total_score'])
        ) {
            $data['total_score'] =
                $data['tracking_score'] +
                $data['obedience_score'] +
                $data['protection_score'];
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