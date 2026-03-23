<?php

namespace App\Services\EventTypes\Show;

use App\Services\EventTypes\BaseEventType;

class ChampionShowModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'champion_show';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Champion Show';

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
            'label' => 'Show Class',
            'options' => [
                'champion',   // Champion Show-n csak ez releváns
            ],
        ],

        'rating' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Rating',
            'options' => [
                'excellent',
                'very_good',
                'good',
                'sufficient',
                'insufficient',
            ],
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 4,
        ],

        'champion_title' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Champion Title Awarded',
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