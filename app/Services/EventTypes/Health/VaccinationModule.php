<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class VaccinationModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'vaccination';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Vaccination';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'vaccine_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Vaccine Name (e.g. Rabies, DHPPi, Lepto)',
        ],

        'vaccine_type' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Vaccine Type',
            'options' => [
                'Core',
                'Non-Core',
                'Rabies',
                'DHPPi',
                'DHP',
                'Parvo',
                'Leptospirosis',
                'Kennel Cough',
                'Lyme',
                'Other',
            ],
        ],

        'manufacturer' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Manufacturer (e.g. Nobivac, Vanguard)',
        ],

        'batch_number' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Batch / Lot Number',
        ],

        'veterinarian' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Veterinarian Name',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name',
        ],

        'valid_until' => [
            'type' => 'date',
            'required' => false,
            'label' => 'Valid Until',
        ],

        'booster_required' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Booster Required',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['vaccine_name', 'manufacturer', 'batch_number', 'veterinarian', 'clinic', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha van "valid_until" dátum, és még nem állították be a booster flag-et:
        if (isset($data['valid_until']) && !isset($data['booster_required'])) {
            $data['booster_required'] = true;
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