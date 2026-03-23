<?php

namespace App\Services\EventTypes\Show;

use App\Services\EventTypes\BaseEventType;

class CACIBModule extends BaseEventType
{
    /**
     * A modul egyedi azonosítója.
     */
    public string $key = 'cacib';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'CACIB';

    /**
     * A modul által használt mezok definíciói.
     * Ezeket a mezoket a rendszer automatikusan:
     * - validálja
     * - normalizálja
     * - canonicalizálja
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
                'puppy',
                'junior',
                'intermediate',
                'open',
                'working',
                'champion',
                'veteran',
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
            'label' => 'CACIB Awarded',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Judge Notes',
        ],
    ];

    /**
     * Canonicalizálás – minden adatot egységes formára hozunk.
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
     * Validáció – a BaseEventType automatikusan kezeli,
     * de itt adhatsz hozzá extra szabályokat.
     */
    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}