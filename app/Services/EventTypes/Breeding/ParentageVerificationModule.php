<?php

namespace App\Services\EventTypes\Breeding;

use App\Services\EventTypes\BaseEventType;

class ParentageVerificationModule extends BaseEventType
{
    public string $key = 'parentage_verification';
    public string $name = 'Parentage Verification';

    public array $fields = [

        'puppy_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Puppy ID',
        ],

        'alleged_sire_id' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Alleged Sire ID',
        ],

        'alleged_dam_id' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Alleged Dam ID',
        ],

        'laboratory' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Laboratory Name',
        ],

        'test_method' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Test Method',
            'options' => [
                'Microsatellite STR',
                'SNP Panel',
                'DNA Profiling',
                'Other',
            ],
        ],

        'result' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Result',
            'options' => [
                'Confirmed',
                'Excluded',
                'Inconclusive',
                'Partial Match',
                'Pending',
            ],
        ],

        'confidence_percentage' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Confidence Percentage',
            'min' => 0,
            'max' => 100,
        ],

        'certificate_id' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Certificate ID',
        ],

        'official_certificate' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Official Certificate Provided',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['laboratory', 'certificate_id', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha result = Confirmed ? confidence legyen legalább 95%
        if (($data['result'] ?? null) === 'Confirmed') {
            if (empty($data['confidence_percentage']) || $data['confidence_percentage'] < 95) {
                $data['confidence_percentage'] = 99.0;
            }
        }

        // Ha result = Excluded ? confidence legyen 100%
        if (($data['result'] ?? null) === 'Excluded') {
            $data['confidence_percentage'] = 100.0;
        }

        // Ha nincs certificate, official_certificate = false
        if (empty($data['certificate_id'])) {
            $data['official_certificate'] = false;
        }

        return $data;
    }

    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}