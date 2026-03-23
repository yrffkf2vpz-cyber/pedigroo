<?php

namespace App\Services\EventTypes\Health;

use App\Services\EventTypes\BaseEventType;

class GeneticTestModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'genetic_test';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Genetic Test';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'test_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Test Name (e.g. DM, PRA, MDR1)',
        ],

        'laboratory' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Laboratory Name',
        ],

        'method' => [
            'type' => 'enum',
            'required' => false,
            'label' => 'Testing Method',
            'options' => [
                'PCR',
                'DNA Sequencing',
                'Genotyping',
                'Microarray',
                'Other',
            ],
        ],

        'result' => [
            'type' => 'enum',
            'required' => true,
            'label' => 'Result',
            'options' => [
                'Clear',
                'Carrier',
                'Affected',
                'At Risk',
                'Low Risk',
                'High Risk',
                'Unknown',
            ],
        ],

        'genotype' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Genotype (e.g. N/N, N/DM, DM/DM)',
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
            'label' => 'Laboratory Notes',
        ],
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['test_name', 'laboratory', 'genotype', 'certificate_id', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Ha a result "Clear", akkor a genotype gyakran "N/N"
        if (($data['result'] ?? null) === 'Clear' && empty($data['genotype'])) {
            $data['genotype'] = 'N/N';
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