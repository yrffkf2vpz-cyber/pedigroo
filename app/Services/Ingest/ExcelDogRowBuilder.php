<?php

namespace App\Services\Ingest;

class ExcelDogRowBuilder
{
    public function fromExcelRow(array $row): array
    {
        $cells = $this->normalizeRow($row);
        $classified = $this->classifyCells($cells);

        $name      = $this->pickName($classified);
        $regNo     = $this->pickRegNo($classified);
        $dob       = $this->pickDob($classified);
        $sex       = $this->pickSex($classified);
        $breeder   = $this->pickBreeder($classified);
        $town      = $this->pickTown($classified);
        $origin    = $this->inferOriginCountry($town);
        $standing  = $origin;
        $color     = $this->pickColor($classified);
        $breed     = $this->pickBreed($classified);

        $confidence = $this->computeConfidence([
            'name'     => $name,
            'reg_no'   => $regNo,
            'dob'      => $dob,
            'sex'      => $sex,
            'breeder'  => $breeder,
            'town'     => $town,
            'color'    => $color,
            'breed'    => $breed,
        ]);

        return [
            'source_id'            => null,
            'raw_name'             => $name,
            'raw_reg_no'           => $regNo,
            'raw_fci_no'           => null,
            'raw_color'            => $color,
            'raw_breed'            => $breed,
            'raw_country'          => $origin,
            'raw_breeder'          => $breeder,
            'raw_owner'            => null,
            'raw_kennel'           => null,

            'name'                 => $name,
            'prefix'               => null,
            'firstname'            => null,
            'lastname'             => null,
            'dob'                  => $dob,
            'sex'                  => $sex,
            'color'                => $color,
            'breed'                => $breed,
            'origin_country'       => $origin,
            'standing_country'     => $standing,
            'breeder'              => $breeder,
            'owner'                => null,
            'kennel'               => null,

            'found_on'             => 'excel',
            'confidence'           => $confidence,
        ];
    }

    private function normalizeRow(array $row): array
    {
        $cells = [];
        foreach ($row as $cell) {
            if (is_null($cell)) {
                $cells[] = null;
                continue;
            }
            $cells[] = trim((string)$cell);
        }
        return $cells;
    }

    private function classifyCells(array $cells): array
    {
        $classified = [];

        foreach ($cells as $index => $value) {
            if ($value === null || $value === '') {
                $classified[$index] = ['type' => 'empty', 'value' => null];
                continue;
            }

            // 1) név + reg_no egy cellában?
            $split = $this->splitNameAndRegNo($value);
            if ($split['reg_no']) {
                $classified[$index] = [
                    'type'   => 'dog_name_with_reg',
                    'name'   => $split['name'],
                    'reg_no' => $split['reg_no'],
                ];
                continue;
            }

            // 2) dátum?
            if ($this->isDate($value)) {
                $classified[$index] = ['type' => 'dob', 'value' => $this->toDate($value)];
                continue;
            }

            // 3) sex?
            if ($this->isSex($value)) {
                $classified[$index] = ['type' => 'sex', 'value' => $this->normalizeSex($value)];
                continue;
            }

            // 4) reg_no?
            if ($this->isRegNo($value)) {
                $classified[$index] = ['type' => 'reg_no', 'value' => $value];
                continue;
            }

            // 5) település?
            if ($this->isTown($value)) {
                $classified[$index] = ['type' => 'town', 'value' => $value];
                continue;
            }

            // 6) tenyésztő?
            if ($this->isPersonName($value)) {
                $classified[$index] = ['type' => 'breeder_candidate', 'value' => $value];
                continue;
            }

            // 7) kutyanév?
            if ($this->isLikelyDogName($value)) {
                $classified[$index] = ['type' => 'dog_name', 'value' => $value];
                continue;
            }

            $classified[$index] = ['type' => 'unknown', 'value' => $value];
        }

        return $classified;
    }

    private function splitNameAndRegNo(string $value): array
    {
        // pl: "Zhangaran Fonzi 14015/93"
        if (preg_match('/(.+)\s+([A-Z0-9\.\/\-]+)$/u', $value, $m)) {
            $possibleReg = $m[2];

            if ($this->isRegNo($possibleReg)) {
                return [
                    'name'   => trim($m[1]),
                    'reg_no' => trim($possibleReg),
                ];
            }
        }

        return [
            'name'   => $value,
            'reg_no' => null,
        ];
    }

    private function pickName(array $classified): ?string
    {
        foreach ($classified as $cell) {
            if ($cell['type'] === 'dog_name_with_reg') {
                return $cell['name'];
            }
            if ($cell['type'] === 'dog_name') {
                return $cell['value'];
            }
        }
        return null;
    }

    private function pickRegNo(array $classified): ?string
    {
        foreach ($classified as $cell) {
            if ($cell['type'] === 'dog_name_with_reg') {
                return $cell['reg_no'];
            }
            if ($cell['type'] === 'reg_no') {
                return $cell['value'];
            }
        }
        return null;
    }

    private function pickDob(array $classified): ?string
    {
        foreach ($classified as $cell) {
            if ($cell['type'] === 'dob') {
                return $cell['value'];
            }
        }
        return null;
    }

    private function pickSex(array $classified): ?string
    {
        foreach ($classified as $cell) {
            if ($cell['type'] === 'sex') {
                return $cell['value']; // M / F / null
            }
        }
        return null;
    }

    private function pickBreeder(array $classified): ?string
    {
        foreach ($classified as $cell) {
            if ($cell['type'] === 'breeder_candidate') {
                return $cell['value'];
            }
        }
        return null;
    }

    private function pickTown(array $classified): ?string
    {
        foreach ($classified as $cell) {
            if ($cell['type'] === 'town') {
                return $cell['value'];
            }
        }
        return null;
    }

    private function pickColor(array $classified): ?string
    {
        return null; // később bővíthető
    }

    private function pickBreed(array $classified): ?string
    {
        return null; // később bővíthető
    }

    private function inferOriginCountry(?string $town): ?string
    {
        if (!$town) return null;

        $lower = mb_strtolower($town, 'UTF-8');

        if (str_contains($lower, 'magyar')) return 'HU';
        if (str_contains($lower, 'finn')) return 'FI';
        if (str_contains($lower, 'usa')) return 'US';

        return null;
    }

    private function computeConfidence(array $fields): int
    {
        $score = 0;

        if ($fields['name'])    $score += 30;
        if ($fields['reg_no'])  $score += 25;
        if ($fields['dob'])     $score += 10;
        if ($fields['sex'])     $score += 5;
        if ($fields['breeder']) $score += 10;
        if ($fields['town'])    $score += 5;

        return max(20, min(100, $score));
    }

    private function isDate(string $value): bool
    {
        return strtotime($value) !== false;
    }

    private function toDate(string $value): ?string
    {
        $ts = strtotime($value);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    private function isSex(string $value): bool
    {
        $v = mb_strtolower($value, 'UTF-8');
        return in_array($v, ['m','f','kan','szuka','male','female','♂','♀'], true);
    }

    private function normalizeSex(string $value): ?string
    {
        $v = mb_strtolower($value, 'UTF-8');
        if (in_array($v, ['m','kan','male','♂'], true)) return 'M';
        if (in_array($v, ['f','szuka','female','♀'], true)) return 'F';
        return null;
    }

    private function isRegNo(string $value): bool
    {
        $v = strtoupper($value);

        if (preg_match('/\d+\/\d+/', $v)) return true;
        if (preg_match('/[A-Z]{2,}.*\d+/', $v)) return true;

        return false;
    }

    private function isTown(string $value): bool
    {
        if ($this->isDate($value) || $this->isRegNo($value)) return false;
        if (preg_match('/^\d+$/', $value)) return false;

        return true;
    }

    private function isPersonName(string $value): bool
    {
        $parts = preg_split('/\s+/', trim($value));
        if (count($parts) === 0 || count($parts) > 3) return false;

        foreach ($parts as $p) {
            if ($p === '') continue;
            $first = mb_substr($p, 0, 1, 'UTF-8');
            if ($first !== mb_strtoupper($first, 'UTF-8')) return false;
        }

        return true;
    }

    private function isLikelyDogName(string $value): bool
    {
        if ($this->isDate($value) || $this->isRegNo($value) || $this->isSex($value)) return false;

        $parts = preg_split('/\s+/', trim($value));
        if (count($parts) < 1 || count($parts) > 6) return false;

        return true;
    }
}