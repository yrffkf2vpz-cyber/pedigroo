<?php

namespace App\Services\EventTypes\Show;

use App\Services\EventTypes\BaseEventType;

class ClubShowModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'club_show';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Club Show';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'club_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Organizing Club',
        ],

        'class' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Show Class',
            'options' => [
                'baby',
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

        'club_title' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Club Title Awarded',
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
        foreach (['judge', 'club_name', 'notes'] as $field) {
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