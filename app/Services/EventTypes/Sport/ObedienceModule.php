<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class ObedienceModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'obedience';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Obedience';

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
            'label' => 'Obedience Level',
            'options' => [
                'OB1',
                'OB2',
                'OB3',
            ],
        ],

        // Feladatok pontozása
        'heelwork_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Heelwork Score',
            'min' => 0,
            'max' => 30,
        ],

        'recall_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Recall Score',
            'min' => 0,
            'max' => 20,
        ],

        'retrieve_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Retrieve Score',
            'min' => 0,
            'max' => 20,
        ],

        'sendaway_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Send Away Score',
            'min' => 0,
            'max' => 20,
        ],

        'down_stay_score' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Down Stay Score',
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
        if (isset($data['judge'])) {
            $data['judge'] = trim($data['judge']);
        }

        if (isset($data['notes'])) {
            $data['notes'] = trim($data['notes']);
        }

        // Automatikus total_score számítás
        $scoreFields = [
            'heelwork_score',
            'recall_score',
            'retrieve_score',
            'sendaway_score',
            'down_stay_score',
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