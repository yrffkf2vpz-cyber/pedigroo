<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class EDScreeningModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'ed_screening';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Elbow Dysplasia Screening';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'veterinarian' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Veterinarian Name',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name',
        ],

        'method' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Screening Method',
            'options' => [
                'FCI',
                'OFA',
                'BVA',
                'Other',
            ],
        ],

        'ed_grade' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'ED Grade',
            'options' => [
                '0',
                '1',
                '2',
                '3',
                'OFA Normal',
                'OFA Grade 1',
                'OFA Grade 2',
                'OFA Grade 3',
                'BVA 0',
                'BVA 1',
                'BVA 2',
                'BVA 3',
                'Unknown',
            ],
        ],

        'left_score' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Left Elbow Score',
        ],

        'right_score' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Right Elbow Score',
        ],

        'official_certificate' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Official Certificate Provided',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Veterinarian Notes',
        ],
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['veterinarian', 'clinic', 'left_score', 'right_score', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
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