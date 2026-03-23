<?php

namespace App\Services\Normalizers;

use App\Models\PdDiagnosisMap;

class DiagnosisNormalizeEngine
{
    public function normalize(array $rawDiagnoses, string $breedCode): array
    {
        // rawDiagnoses pl.:
        // [
        //   'hd' => 'HD A',
        //   'ed' => '0',
        //   'other' => 'boas 1'
        // ]

        $hdRaw    = $rawDiagnoses['hd']    ?? null;
        $edRaw    = $rawDiagnoses['ed']    ?? null;
        $otherRaw = $rawDiagnoses['other'] ?? null;

        return [
            'diagnosisHdNormalized'      => $this->normalizeHd($hdRaw, $breedCode),
            'diagnosisEdNormalized'      => $this->normalizeEd($edRaw, $breedCode),
            'diagnosisOtherNormalized'   => $this->normalizeOther($otherRaw, $breedCode),
        ];
    }

    protected function normalizeHd(?string $value, string $breedCode): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim(mb_strtolower($value));

        // 1) explicit mapping tábla
        if ($mapped = $this->lookupMap($breedCode, $value, 'HD')) {
            return $mapped;
        }

        // 2) egyszeru minták
        if (preg_match('/\b(a|a1|0)\b/u', $value)) {
            return 'HD-A';
        }

        if (preg_match('/\b(b|b1)\b/u', $value)) {
            return 'HD-B';
        }

        if (preg_match('/\b(c|c1)\b/u', $value)) {
            return 'HD-C';
        }

        if (preg_match('/\b(d|d1)\b/u', $value)) {
            return 'HD-D';
        }

        if (preg_match('/\b(e|e1)\b/u', $value)) {
            return 'HD-E';
        }

        // 3) fallback: visszaadjuk az eredetit (audit miatt)
        return $value;
    }

    protected function normalizeEd(?string $value, string $breedCode): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim(mb_strtolower($value));

        // 1) explicit mapping tábla
        if ($mapped = $this->lookupMap($breedCode, $value, 'ED')) {
            return $mapped;
        }

        // 2) egyszeru minták
        if (preg_match('/(ed[\s\-]*0|\b0\b|free|normal)/u', $value)) {
            return 'ED-0';
        }

        if (preg_match('/(ed[\s\-]*1|\b1\b|mild)/u', $value)) {
            return 'ED-1';
        }

        if (preg_match('/(ed[\s\-]*2|\b2\b|moderate)/u', $value)) {
            return 'ED-2';
        }

        if (preg_match('/(ed[\s\-]*3|\b3\b|severe)/u', $value)) {
            return 'ED-3';
        }

        // 3) fallback
        return $value;
    }

    protected function normalizeOther(?string $value, string $breedCode): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim(mb_strtolower($value));

        // 1) explicit mapping tábla
        if ($mapped = $this->lookupMap($breedCode, $value, 'OTHER')) {
            return $mapped;
        }

        // 2) egyszeru példák (bovítheto)
        if (preg_match('/boas[\s\-]*1|boas i/u', $value)) {
            return 'BOAS-1';
        }

        if (preg_match('/boas[\s\-]*2|boas ii/u', $value)) {
            return 'BOAS-2';
        }

        if (preg_match('/boas[\s\-]*3|boas iii/u', $value)) {
            return 'BOAS-3';
        }

        // 3) fallback
        return $value;
    }

    protected function lookupMap(string $breedCode, string $rawValue, string $category): ?string
    {
        $record = PdDiagnosisMap::query()
            ->where('breed_code', $breedCode)
            ->where('category', $category)
            ->where('is_active', true)
            ->whereRaw('LOWER(raw_value) = ?', [mb_strtolower($rawValue)])
            ->first();

        return $record?->normalized_code;
    }
}