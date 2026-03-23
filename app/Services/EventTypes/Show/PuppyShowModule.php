<?php

namespace App\Services\EventTypes\Show;

use App\Services\EventTypes\BaseEventType;

class PuppyShowModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'puppy_show';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Puppy Show';

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
            'label' => 'Puppy Class',
            'options' => [
                'baby',      // 3–6 hónap
                'puppy',     // 6–9 hónap
                'junior_puppy', // 9–12 hónap
            ],
        ],

        'rating' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Rating',
            'options' => [
                'very_promising',
                'promising',
                'less_promising',
            ],
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 4,
        ],

        'best_puppy' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Best Puppy Awarded',
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