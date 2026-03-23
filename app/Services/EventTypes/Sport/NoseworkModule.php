<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class NoseworkModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'nosework';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Nosework';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'level' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Nosework Level',
            'options' => [
                'NW1',
                'NW2',
                'NW3',
                'Elite',
            ],
        ],

        // Konténer keresés
        'container_time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Container Search Time (seconds)',
            'min' => 0.01,
        ],

        'container_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Container Faults',
            'min' => 0,
        ],

        'container_passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Container Search Passed',
        ],

        // Jármu keresés
        'vehicle_time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Vehicle Search Time (seconds)',
            'min' => 0.01,
        ],

        'vehicle_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Vehicle Faults',
            'min' => 0,
        ],

        'vehicle_passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Vehicle Search Passed',
        ],

        // Beltéri keresés
        'interior_time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Interior Search Time (seconds)',
            'min' => 0.01,
        ],

        'interior_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Interior Faults',
            'min' => 0,
        ],

        'interior_passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Interior Search Passed',
        ],

        // Kültéri keresés
        'exterior_time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Exterior Search Time (seconds)',
            'min' => 0.01,
        ],

        'exterior_faults' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Exterior Faults',
            'min' => 0,
        ],

        'exterior_passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Exterior Search Passed',
        ],

        // Összesített eredmények
        'overall_passed' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Overall Passed',
        ],

        'total_time' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Total Time (seconds)',
            'min' => 0.01,
        ],

        'total_faults' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Total Faults',
            'min' => 0,
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

        // Total time számítása
        if (
            !isset($data['total_time']) &&
            isset($data['container_time'], $data['vehicle_time'], $data['interior_time'], $data['exterior_time'])
        ) {
            $data['total_time'] =
                $data['container_time'] +
                $data['vehicle_time'] +
                $data['interior_time'] +
                $data['exterior_time'];
        }

        // Total faults számítása
        if (
            !isset($data['total_faults']) &&
            isset($data['container_faults'], $data['vehicle_faults'], $data['interior_faults'], $data['exterior_faults'])
        ) {
            $data['total_faults'] =
                $data['container_faults'] +
                $data['vehicle_faults'] +
                $data['interior_faults'] +
                $data['exterior_faults'];
        }

        // Overall passed
        if (!isset($data['overall_passed'])) {
            $data['overall_passed'] =
                ($data['container_passed'] ?? false) &&
                ($data['vehicle_passed'] ?? false) &&
                ($data['interior_passed'] ?? false) &&
                ($data['exterior_passed'] ?? false);
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