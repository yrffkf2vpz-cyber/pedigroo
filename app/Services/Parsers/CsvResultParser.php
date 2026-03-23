<?php

namespace App\Services\Parsers;

use App\Services\Normalizers\DogNormalizer;

class CsvResultParser
{
    public function parseFile(string $path): array
    {
        $rows = $this->readCsv($path);

        if (empty($rows)) {
            return $this->emptyEvent();
        }

        // Header normalizálás
        $headers = $this->normalizeHeaders(array_shift($rows));

        $results = [];
        foreach ($rows as $row) {
            $mapped = $this->mapRow($headers, $row);

            // Normalizált kutya adatok
            $dogData = [
                "dog_name"  => $mapped['dog_name'] ?? "",
                "breed"     => $mapped['breed'] ?? "",
                "class"     => $mapped['class'] ?? "",
                "placement" => $mapped['placement'] ?? "",
                "title"     => $mapped['title'] ?? "",
                "cac"       => $mapped['cac'] ?? "",
                "rescac"    => $mapped['rescac'] ?? "",
                "cacib"     => $mapped['cacib'] ?? "",
                "rescacib"  => $mapped['rescacib'] ?? "",
                "hpj"       => $mapped['hpj'] ?? "",
                "bob"       => $mapped['bob'] ?? "",
                "bis"       => $mapped['bis'] ?? "",
                "ring"      => $mapped['ring'] ?? "",
                "judge"     => $mapped['judge'] ?? "",
                "reg_no"    => $this->extractRegNo($mapped['dog_name'] ?? ""),
                "raw_line"  => implode(" | ", $row),
            ];

            // Dog normalizer integráció
            // dog_id lekérése
$dogId = \App\Services\Normalizers\DogNormalizer::id(
    $dogData['dog_name'] ?? null,
    $dogData['breed'] ?? null,
    $dogData['reg_no'] ?? null
);

// dog_id hozzáadása
$dogData['dog_id'] = $dogId;

// normalizált adat mentése
$results[] = $dogData;
        }

        // Bírók és ringek összegyűjtése
        $judges = array_unique(array_filter(array_column($results, 'judge')));
        $rings  = array_unique(array_filter(array_column($results, 'ring')));

        return [
            "event_name" => "CSV Event",
            "date"       => date('Y-m-d'),
            "country"    => "Unknown",
            "city"       => "Unknown",
            "location"   => "Unknown",
            "club"       => "Unknown",
            "judges"     => $judges,
            "rings"      => $rings,
            "results"    => $results,
        ];
    }

    /* ---------------------------------------------------------
     *  CSV READER
     * --------------------------------------------------------- */

    protected function readCsv(string $path): array
    {
        $rows = [];
        if (!file_exists($path)) {
            return $rows;
        }

        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 10000, ';')) !== false) {
                if (count($data) === 1) {
                    $data = explode(',', $data[0]);
                }
                $rows[] = array_map('trim', $data);
            }
            fclose($handle);
        }

        return $rows;
    }

    /* ---------------------------------------------------------
     *  HEADER NORMALIZATION
     * --------------------------------------------------------- */

    protected function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $index => $header) {
            $h = strtolower(trim($header));

            // Kutya neve
            if (in_array($h, ['dog', 'dog_name', 'kutya', 'név'])) {
                $normalized[$index] = 'dog_name';

            // Fajta
            } elseif (in_array($h, ['breed', 'fajta'])) {
                $normalized[$index] = 'breed';

            // Osztály
            } elseif (in_array($h, ['class', 'osztály'])) {
                $normalized[$index] = 'class';

            // Helyezés
            } elseif (in_array($h, ['placement', 'helyezés'])) {
                $normalized[$index] = 'placement';

            // Cím
            } elseif (in_array($h, ['title', 'cím'])) {
                $normalized[$index] = 'title';

            // FCI címek
            } elseif ($h === 'cac') {
                $normalized[$index] = 'cac';
            } elseif (in_array($h, ['rescac', 'res_cac'])) {
                $normalized[$index] = 'rescac';
            } elseif (in_array($h, ['cacib'])) {
                $normalized[$index] = 'cacib';
            } elseif (in_array($h, ['rescacib', 'res_cacib'])) {
                $normalized[$index] = 'rescacib';
            } elseif (in_array($h, ['hpj', 'juniorwinner'])) {
                $normalized[$index] = 'hpj';
            } elseif ($h === 'bob') {
                $normalized[$index] = 'bob';
            } elseif ($h === 'bis') {
                $normalized[$index] = 'bis';

            // Ring
            } elseif (in_array($h, ['ring', 'ring_no', 'ring_number'])) {
                $normalized[$index] = 'ring';

            // Bíró
            } elseif (in_array($h, ['judge', 'bíró', 'judge_name'])) {
                $normalized[$index] = 'judge';

            // Egyéb
            } else {
                $normalized[$index] = $h;
            }
        }

        return $normalized;
    }

    /* ---------------------------------------------------------
     *  ROW MAPPING
     * --------------------------------------------------------- */

    protected function mapRow(array $headers, array $row): array
    {
        $mapped = [];

        foreach ($headers as $index => $key) {
            $mapped[$key] = $row[$index] ?? null;
        }

        return $mapped;
    }

    /* ---------------------------------------------------------
     *  REGISTRATION NUMBER EXTRACTION
     * --------------------------------------------------------- */

    protected function extractRegNo(string $text): ?string
    {
        $pattern = '/([0-9]{1,6}[A-Z]?(?:\/[A-Z])?\/[0-9]{1,4})/i';

        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /* ---------------------------------------------------------
     *  EMPTY EVENT
     * --------------------------------------------------------- */

    protected function emptyEvent(): array
    {
        return [
            "event_name" => "CSV Event",
            "date"       => date('Y-m-d'),
            "country"    => "Unknown",
            "city"       => "Unknown",
            "location"   => "Unknown",
            "club"       => "Unknown",
            "judges"     => [],
            "rings"      => [],
            "results"    => [],
        ];
    }
}