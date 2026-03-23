<?php

namespace App\Services\EventTypes\Show;

use App\Services\EventTypes\BaseEventType;

class CACModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'cac';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'CAC';

    /**
     * A modul mezodefiníciói.
     * A rendszer automatikusan validálja és normalizálja.
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
                'junior',
                'intermediate',
                'open',
                'working',
                'champion',
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

        'title_awarded' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'CAC Awarded',
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