<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class MondioringModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'mondioring';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Mondioring';

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
            'label' => 'Mondioring Level',
            'options' => [
                'MR1',
                'MR2',
                'MR3',
            ],
        ],

        // Obedience szekció
        'obedience_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Obedience Score',
            'min' => 0,
            'max' => 100,
        ],

        // Jumps szekció
        'jumps_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Jumps Score',
            'min' => 0,
            'max' => 100,
        ],

        // Protection szekció
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

        'decoy_name' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Decoy Name',
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
        foreach (['judge', 'decoy_name', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Total score automatikus számítása
        if (
            !isset($data['total_score']) &&
            isset($data['obedience_score'], $data['jumps_score'], $data['protection_score'])
        ) {
            $data['total_score'] =
                $data['obedience_score'] +
                $data['jumps_score'] +
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