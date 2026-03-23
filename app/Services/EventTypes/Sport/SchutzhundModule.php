<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class SchutzhundModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'schutzhund';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Schutzhund';

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
            'label' => 'Schutzhund Level',
            'options' => [
                'SchH1',
                'SchH2',
                'SchH3',
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
        foreach (['judge', 'helper_name', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Total score automatikus számítása
        if (
            !isset($data['total_score']) &&
            isset($data['tracking_score'], $data['obedience_score'], $data['protection_score'])
        ) {
            $data['total_score'] =
                $data['tracking_score'] +
                $data['obedience_score'] +
                $data['protection_score'];
        }

        // Passed státusz (általában 70% felett)
        if (isset($data['total_score']) && !isset($data['passed'])) {
            $data['passed'] = $data['total_score'] >= 210; // 300 pontból 70%
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