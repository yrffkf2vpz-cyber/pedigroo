<?php

namespace App\Services\EventTypes\Breeding;

use App\Services\EventTypes\BaseEventType;

class BreedingExamModule extends BaseEventType
{
    public string $key = 'breeding_exam';
    public string $name = 'Breeding Examination';

    public array $fields = [

        'dog_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Dog ID',
        ],

        'exam_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Exam Type',
            'options' => [
                'Conformation Only',
                'Behavior Only',
                'Full Breeding Exam',
                'Club-Specific Exam',
                'Other',
            ],
        ],

        'organization' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Organization (e.g. FCI, UKC, ABKC, Breed Club)',
        ],

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'conformation_rating' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Conformation Rating',
            'options' => [
                'Excellent',
                'Very Good',
                'Good',
                'Satisfactory',
                'Insufficient',
                'Not Evaluated',
            ],
        ],

        'behavior_rating' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Behavior Rating',
            'options' => [
                'Stable',
                'Good',
                'Acceptable',
                'Unstable',
                'Aggressive',
                'Fearful',
                'Not Evaluated',
            ],
        ],

        'faults' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Observed Faults',
        ],

        'disqualifying_faults' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Disqualifying Faults',
        ],

        'final_result' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Final Result',
            'options' => [
                'Approved',
                'Approved with Conditions',
                'Not Approved',
                'Deferred',
            ],
        ],

        'valid_until' => [
            'type' => 'date',
            'required' => false,
            'label' => 'Valid Until',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Additional Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['organization', 'judge', 'faults', 'disqualifying_faults', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha vannak disqualifying faults ? final_result nem lehet Approved
        if (!empty($data['disqualifying_faults'])) {
            if (($data['final_result'] ?? null) === 'Approved') {
                $data['final_result'] = 'Not Approved';
            }
        }

        // Ha final_result = Approved with Conditions ? legyen valid_until
        if (($data['final_result'] ?? null) === 'Approved with Conditions' &&
            empty($data['valid_until'])) {
            // nem generálunk dátumot, csak biztosítjuk, hogy a mezo szerepeljen
            $data['valid_until'] = null;
        }

        return $data;
    }

    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}