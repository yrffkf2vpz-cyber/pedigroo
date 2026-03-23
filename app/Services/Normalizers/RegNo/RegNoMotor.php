<?php

namespace App\Services\Normalizers\RegNo;

class RegNoMotor
{
    public function __construct(
        protected OrganizationDetector $orgDetector,
        protected BreedCodeDetector $breedDetector,
        protected SequenceDetector $seqDetector,
        protected StatusDetector $statusDetector,
        protected YearDetector $yearDetector,
    ) {}

    /**
     * Fő elemző metódus – minden réteg külön modulban.
     */
    public function analyze(?string $raw, array $context = []): ?array
    {
        if (!$raw) {
            return [
                'raw'            => null,
                'normalized'     => null,
                'classification' => 'legacy',
                'status'         => 'invalid',
            ];
        }

        $clean = trim($raw);

        // 1) szervezet + rendszer réteg
        $orgInfo = $this->orgDetector->detect($clean);
        $org     = $orgInfo['organization'] ?? null;
        $layer   = $orgInfo['layer'] ?? 'unknown';
        $country = $orgInfo['country'] ?? null;

        // 2) fajta – reg_no + context alapján
        $breed = $this->breedDetector->detect($clean, $org, $context);

        // 3) sorszám
        $seq = $this->seqDetector->detect($clean);

        // 4) státusz
        $status = $this->statusDetector->detect($clean);

        // 5) év
        $year = $this->yearDetector->detect($clean);

        // 6) normalizált forma
        $normalized = $this->buildNormalized($org, $breed, $seq, $status, $year);

        // 7) confidence
        $confidence = $this->computeConfidence($org, $breed, $seq, $status, $year);

        // 8) korszak felismerése
        $classification = $this->detectEra($year, $orgInfo);

        return [
            'raw'               => $raw,

            'organization'      => $org,
            'system_layer'      => $layer,
            'country'           => $country,

            'breed_name'        => $breed['name'] ?? null,
            'breed_confidence'  => $breed['confidence'] ?? null,
            'breed_code'        => $breed['code'] ?? null,

            'yearly_sequence'   => $seq,

            'status_code'       => $status['code'] ?? null,
            'status_meaning'    => $status['meaning'] ?? null,

            'year'              => $year,

            'normalized'        => $normalized,
            'confidence'        => $confidence,

            // ÚJ: korszak besorolás
            'classification'    => $classification,
        ];
    }

    /**
     * Normalizált reg_no összeállítása.
     */
    protected function buildNormalized(?string $org, ?array $breed, ?int $seq, ?array $status, ?int $year): ?string
    {
        if (!$org && !$breed && !$seq) {
            return null;
        }

        $parts = [];

        $parts[] = $org ?: 'UNKNOWN';

        if (!empty($breed['name'])) {
            $parts[] = strtoupper($breed['name']);
        }

        if ($seq) {
            $seqPart = (string)$seq;

            if (!empty($status['code'])) {
                $seqPart .= '/' . strtoupper($status['code']);
            }

            if ($year) {
                $seqPart .= '/' . $year;
            }

            $parts[] = $seqPart;
        }

        return implode('-', $parts);
    }

    /**
     * Confidence számítás.
     */
    protected function computeConfidence(...$pieces): float
    {
        [$org, $breed, $seq, $status, $year] = $pieces;

        $score = 0.0;

        if ($org)   $score += 0.25;
        if ($breed) $score += 0.30;
        if ($seq)   $score += 0.20;
        if ($year)  $score += 0.15;
        if ($status)$score += 0.10;

        return min(1.0, $score);
    }

    /**
     * ������ KORSZAK FELISMERÉSE (modern / historical / legacy)
     */
    protected function detectEra(?int $year, array $orgInfo): string
    {
        // prefix korszak (OrganizationDetector tölti ki)
        $isModernPrefix = $orgInfo['prefix_is_modern'] ?? false;

        // nincs év → legacy
        if (!$year) {
            return 'legacy';
        }

        // modern korszak
        if ($year >= 1990 && $isModernPrefix) {
            return 'modern';
        }

        // historical korszak
        if ($year >= 1960 && $year < 1990) {
            return 'historical';
        }

        // minden más → legacy
        return 'legacy';
    }
}