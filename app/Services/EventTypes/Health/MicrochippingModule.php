<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class MicrochippingModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'microchipping';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Microchipping';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'chip_number' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Microchip Number',
        ],

        'chip_type' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Chip Type',
            'options' => [
                'ISO 11784/11785',
                'FDX-A',
                'FDX-B',
                'HDX',
                'Other',
            ],
        ],

        'implant_location' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Implant Location (e.g. left shoulder)',
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

        'registry' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Registry Database (e.g. PetVetData, AKC Reunite)',
        ],

        'registered' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Registered in Database',
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
        foreach (['chip_number', 'implant_location', 'veterinarian', 'clinic', 'registry', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Chip szám nagybetusítése (gyakori formátum)
        if (isset($data['chip_number'])) {
            $data['chip_number'] = strtoupper($data['chip_number']);
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