<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class SurgeryModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'surgery';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Surgery';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'procedure_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Procedure Name (e.g. Neutering, TPLO, Tumor Removal)',
        ],

        'procedure_type' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Procedure Type',
            'options' => [
                'Soft Tissue',
                'Orthopedic',
                'Dental',
                'Emergency',
                'Reconstructive',
                'Oncology',
                'Other',
            ],
        ],

        'veterinarian' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Surgeon / Veterinarian',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name',
        ],

        'anesthesia_type' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Anesthesia Type',
            'options' => [
                'General',
                'Local',
                'Sedation',
                'Regional Block',
                'Other',
            ],
        ],

        'duration_minutes' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Duration (minutes)',
            'min' => 1,
            'max' => 600,
        ],

        'complications' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Complications (if any)',
        ],

        'post_op_instructions' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Post-Operative Instructions',
        ],

        'follow_up_required' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Follow-Up Required',
        ],

        'follow_up_date' => [
            'type' => 'date',
            'required' => false,
            'label' => 'Follow-Up Date',
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
        foreach (['procedure_name', 'veterinarian', 'clinic', 'complications', 'post_op_instructions', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha nincs follow-up dátum, akkor ne legyen follow-up flag
        if (empty($data['follow_up_date'])) {
            $data['follow_up_required'] = false;
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