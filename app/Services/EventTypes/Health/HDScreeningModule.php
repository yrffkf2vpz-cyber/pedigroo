<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class HDScreeningModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'hd_screening';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Hip Dysplasia Screening';

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
                'PennHIP',
                'BVA',
                'Other',
            ],
        ],

        'hd_grade' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'HD Grade',
            'options' => [
                'A', 'A1', 'A2',
                'B', 'B1', 'B2',
                'C', 'C1', 'C2',
                'D', 'D1', 'D2',
                'E', 'E1', 'E2',
                'OFA Excellent',
                'OFA Good',
                'OFA Fair',
                'OFA Borderline',
                'OFA Mild',
                'OFA Moderate',
                'OFA Severe',
                'PennHIP DI',
                'Unknown',
            ],
        ],

        'pennhip_di' => [
            'type' => 'float',
            'required' => false,
            'label' => 'PennHIP Distraction Index',
            'min' => 0.0,
            'max' => 1.0,
        ],

        'left_score' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Left Hip Score',
        ],

        'right_score' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Right Hip Score',
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

        // PennHIP DI csak akkor legyen, ha a method PennHIP
        if (($data['method'] ?? null) !== 'PennHIP') {
            unset($data['pennhip_di']);
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
