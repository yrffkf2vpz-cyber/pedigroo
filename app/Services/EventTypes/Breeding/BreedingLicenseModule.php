<?php

namespace App\Services\EventTypes\Breeding;

use App\Services\EventTypes\BaseEventType;

class BreedingLicenseModule extends BaseEventType
{
    public string $key = 'breeding_license';
    public string $name = 'Breeding License';

    public array $fields = [

        'dog_id' => [
            'type' => 'integer',
            'required' => true,
            'label' => 'Dog ID',
        ],

        'license_type' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'License Type',
            'options' => [
                'Full License',
                'Conditional License',
                'Temporary License',
                'Club-Specific License',
                'Restricted License',
                'Other',
            ],
        ],

        'organization' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Issuing Organization (e.g. FCI, Breed Club)',
        ],

        'issued_by' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Issued By (Judge or Committee)',
        ],

        'issue_date' => [
            'type' => 'date',
            'required' => true,
            'label' => 'Issue Date',
        ],

        'valid_until' => [
            'type' => 'date',
            'required' => false,
            'label' => 'Valid Until',
        ],

        'conditions' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Conditions (if any)',
        ],

        'status' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'License Status',
            'options' => [
                'Active',
                'Expired',
                'Revoked',
                'Suspended',
                'Pending',
            ],
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Additional Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['organization', 'issued_by', 'conditions', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha a license_type "Conditional" ? legyen conditions mezo
        if (($data['license_type'] ?? null) === 'Conditional' && empty($data['conditions'])) {
            $data['conditions'] = 'Conditions apply';
        }

        // Ha van valid_until, de status = Active ? oké
        // Ha nincs valid_until, de status = Active ? engedjük, mert lehet "lifetime"
        // Ha status = Expired ? legyen valid_until múltban (nem generálunk, csak logikailag tisztítunk)
        if (($data['status'] ?? null) === 'Expired' && empty($data['valid_until'])) {
            $data['valid_until'] = null;
        }

        return $data;
    }

    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}