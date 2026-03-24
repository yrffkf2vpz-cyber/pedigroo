<?php

namespace App\Services\Ingest;

class DogRecordBuilder
{
    /**
     * Excel sor → egységes raw dog rekord
     */
    public function fromExcelRow(array $row): array
{
    $mapped = $this->mapFields($row);

    return [
        'raw_name'    => $this->buildRawNameFromExcel($row),
        'raw_color'   => $mapped['color']   ?? null,
        'raw_breed'   => $mapped['breed']   ?? null,
        'raw_country' => $mapped['country'] ?? null,
        'raw_reg_no'  => $mapped['reg_no']  ?? null,

        'parents'  => $this->buildParentsFromExcel($row),
        'children' => $this->buildChildrenFromExcel($row),
        'results'  => $this->buildResultsFromExcel($row),

        // ⭐ ADD THIS ⭐
        'health'   => $mapped['health'] ?? $row['health'] ?? [],

        'found_on' => 'excel',
        'source_id'=> $mapped['id'] ?? null,
    ];
}

    /**
     * API JSON → egységes raw dog rekord
     */
    public function fromApiPayload(array $payload): array
    {
        $mapped = $this->mapFields($payload);

        return [
            'raw_name'    => $mapped['name']    ?? null,
            'raw_color'   => $mapped['color']   ?? null,
            'raw_breed'   => $mapped['breed']   ?? null,
            'raw_country' => $mapped['country'] ?? null,
            'raw_reg_no'  => $mapped['reg_no']  ?? null,

            'parents' => [
                'father' => $mapped['father_name'] ?? null,
                'mother' => $mapped['mother_name'] ?? null,
            ],
            'children'=> $mapped['children'] ?? [],
            'results' => $mapped['results']  ?? [],
            
            // ⭐ ADD THIS ⭐
            'health'   => $mapped['health'] ?? $row['health'] ?? [],

            'found_on' => 'api',
            'source_id'=> $mapped['id'] ?? null,
        ];
    }

    /**
     * Scraper adat → egységes raw dog rekord
     */
    public function fromScraper(array $data): array
    {
        $mapped = $this->mapFields($data);

        return [
            'raw_name'    => $mapped['full_name'] ?? null,
            'raw_color'   => $mapped['color']     ?? null,
            'raw_breed'   => $mapped['breed']     ?? null,
            'raw_country' => $mapped['country']   ?? null,
            'raw_reg_no'  => $mapped['reg_no']    ?? null,

            'parents' => [
                'father' => $mapped['sire'] ?? null,
                'mother' => $mapped['dam']  ?? null,
            ],
            'children'=> $mapped['offspring'] ?? [],
            'results' => $mapped['results']   ?? [],

            // ⭐ ADD THIS ⭐
            'health'   => $mapped['health'] ?? $row['health'] ?? [],

            'found_on' => 'scraper',
            'source_id'=> $mapped['external_id'] ?? null,
        ];
    }

    /**
     * PDF sor → egységes raw dog rekord
     */
    public function fromPdfRow(array $row): array
    {
        $mapped = $this->mapFields($row);

        return [
            'raw_name'    => $mapped['name']    ?? null,
            'raw_color'   => $mapped['color']   ?? null,
            'raw_breed'   => $mapped['breed']   ?? null,
            'raw_country' => $mapped['country'] ?? null,
            'raw_reg_no'  => $mapped['reg_no']  ?? null,

            'parents' => [
                'father' => $mapped['father'] ?? null,
                'mother' => $mapped['mother'] ?? null,
            ],

            'children' => $mapped['children'] ?? [],
            'results'  => $mapped['results']  ?? [],

            // ⭐ ADD THIS ⭐
            'health'   => $mapped['health'] ?? $row['health'] ?? [],

            'found_on' => 'pdf',
            'source_id'=> $mapped['source_id'] ?? null,
        ];
    }

    // ---------------------------------------------------------
    // Mezőnév normalizálás ingest_rules alapján
    // ---------------------------------------------------------

    private function mapFields(array $row): array
    {
        $rules = config('ingest_rules.fields', []);
        $mapped = [];

        foreach ($row as $key => $value) {
            $normalizedKey = $this->mapFieldName($key, $rules);
            $mapped[$normalizedKey] = $value;
        }

        return $mapped;
    }

    private function mapFieldName(string|int $key, array $rules): string
    {
        // numerikus index → változatlan
        if (is_int($key)) {
            return (string)$key;
        }

        $clean = strtolower(trim($key));

        foreach ($rules as $target => $variants) {
            foreach ($variants as $variant) {
                if ($clean === strtolower(trim($variant))) {
                    return $target;
                }
            }
        }

        return $clean;
    }

    // ---------------------------------------------------------
    // Segédfüggvények – Excel specifikus
    // ---------------------------------------------------------

    private function buildRawNameFromExcel(array $row): ?string
    {
        $raw = $row[1] ?? null;

        if (!$raw) {
            return null;
        }

        $parts = explode(' ', trim($raw));
        $last = array_pop($parts);

        if (preg_match('/\d+\/\d+/', $last)) {
            return trim(implode(' ', $parts));
        }

        return trim($raw);
    }

    private function buildParentsFromExcel(array $row): array
    {
        return [
            'father' => $row[2] ?? null,
            'mother' => $row[3] ?? null,
        ];
    }

    private function buildChildrenFromExcel(array $row): array
    {
        return [];
    }

    private function buildResultsFromExcel(array $row): array
    {
        return [];
    }
}