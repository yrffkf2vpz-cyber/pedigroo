<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class PhysiotherapyModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'physiotherapy';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Physiotherapy Session';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'therapy_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Therapy Type',
            'options' => [
                'Hydrotherapy',
                'Laser Therapy',
                'Ultrasound Therapy',
                'Electrotherapy (TENS/NMES)',
                'Manual Therapy',
                'Massage',
                'Stretching',
                'Strength Training',
                'Balance/Coordination Training',
                'Other',
            ],
        ],

        'therapist' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Physiotherapist Name',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name',
        ],

        'session_duration' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Session Duration (minutes)',
            'min' => 5,
            'max' => 180,
        ],

        'intensity_level' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Intensity Level',
            'options' => [
                'Low',
                'Moderate',
                'High',
            ],
        ],

        'target_area' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Target Area (e.g. hind legs, spine)',
        ],

        'progress_notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Progress Notes',
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
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['therapist', 'clinic', 'target_area', 'progress_notes'] as $field) {
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