<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class RehabilitationModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'rehabilitation';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Rehabilitation Program';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'program_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Program Name (e.g. Post-TPLO Rehab, Neurological Recovery)',
        ],

        'primary_goal' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Primary Goal (e.g. regain mobility, reduce pain)',
        ],

        'therapist' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Rehabilitation Specialist',
        ],

        'clinic' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Clinic Name',
        ],

        'start_date' => [
            'type' => 'date',
            'required' => true,
            'label' => 'Program Start Date',
        ],

        'end_date' => [
            'type' => 'date',
            'required' => false,
            'label' => 'Program End Date',
        ],

        'session_frequency' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Session Frequency',
            'options' => [
                'Daily',
                'Every 2 Days',
                'Weekly',
                'Biweekly',
                'Monthly',
                'Other',
            ],
        ],

        'therapy_components' => [
            'type' => 'array',
            'required' => false,
            'label' => 'Therapy Components',
            'items' => [
                'type' => 'string',
            ],
        ],

        'progress_notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Progress Notes',
        ],

        'outcome' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Outcome',
            'options' => [
                'Excellent',
                'Good',
                'Fair',
                'Poor',
                'Incomplete',
                'Ongoing',
            ],
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
        foreach (['program_name', 'primary_goal', 'therapist', 'clinic', 'progress_notes', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha nincs end_date, outcome ne legyen "Excellent" vagy "Completed"
        if (empty($data['end_date']) && isset($data['outcome'])) {
            if (in_array($data['outcome'], ['Excellent', 'Good', 'Fair', 'Poor'])) {
                $data['outcome'] = 'Ongoing';
            }
        }

        // Ha nincs follow-up dátum ? follow-up flag = false
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