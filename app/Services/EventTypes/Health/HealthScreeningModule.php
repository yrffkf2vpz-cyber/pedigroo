<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class HealthScreeningModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'health_screening';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'General Health Screening';

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

        'weight' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Weight (kg)',
            'min' => 0.1,
            'max' => 150,
        ],

        'body_condition_score' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Body Condition Score (BCS)',
            'options' => [
                '1 - Emaciated',
                '2 - Very Thin',
                '3 - Thin',
                '4 - Underweight',
                '5 - Ideal',
                '6 - Overweight',
                '7 - Heavy',
                '8 - Obese',
                '9 - Morbidly Obese',
            ],
        ],

        'temperature' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Temperature (°C)',
            'min' => 30.0,
            'max' => 45.0,
        ],

        'heart_rate' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Heart Rate (bpm)',
            'min' => 20,
            'max' => 300,
        ],

        'respiratory_rate' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Respiratory Rate (breaths/min)',
            'min' => 5,
            'max' => 200,
        ],

        'overall_health' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Overall Health Assessment',
            'options' => [
                'Excellent',
                'Good',
                'Fair',
                'Poor',
                'Critical',
            ],
        ],

        'recommendations' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Veterinarian Recommendations',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Additional Notes',
        ],
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['veterinarian', 'clinic', 'recommendations', 'notes'] as $field) {
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