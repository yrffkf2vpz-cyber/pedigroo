<?php

namespace App\Services\Normalizers;

use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeHealthService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes egészségügyi normalizálás:
     * - HD / ED / DM / CEA / PRA / MDR1 / HSF4 / IVDD / BAER / ECVO / HEART
     * - többnyelvu formátumok
     * - labor felismerés (OFA, FCI, BVA, SV, ÖKV, CMKU, SKJ, JKC, CKC, ANKC, NZKC)
     * - canonical form (type, value, lab, date, notes)
     */
    public function normalize(
        array $rawHealth,
        ?string $breed = null,
        ?string $country = null,
        bool $debug = false
    ): array {
        $items = [];
        $debugLines = [];

        foreach ($rawHealth as $line) {
            $original = trim((string)$line);
            if ($original === '') {
                continue;
            }

            $clean = mb_strtolower($original);

            $parsed = [
                'raw'   => $original,
                'type'  => null,
                'value' => null,
                'lab'   => null,
                'date'  => null,
                'notes' => null,
            ];

            // HD
            $this->parseHd($original, $clean, $parsed);

            // ED
            $this->parseEd($original, $clean, $parsed);

            // DM
            $this->parseDm($original, $clean, $parsed);

            // CEA
            $this->parseCea($original, $clean, $parsed);

            // PRA
            $this->parsePra($original, $clean, $parsed);

            // MDR1
            $this->parseMdr1($original, $clean, $parsed);

            // HSF4
            $this->parseHsf4($original, $clean, $parsed);

            // IVDD
            $this->parseIvdd($original, $clean, $parsed);

            // BAER
            $this->parseBaer($original, $clean, $parsed);

            // ECVO
            $this->parseEcvo($original, $clean, $parsed);

            // HEART
            $this->parseHeart($original, $clean, $parsed);

            // Dátum (nagyon óvatos, csak év ? YYYY-01-01)
            $this->parseDate($original, $parsed);

            // Labor
            if (!$parsed['lab']) {
                $parsed['lab'] = $this->detectLab($clean);
            }

            if ($parsed['type']) {
                $items[] = $parsed;
            }

            if ($debug) {
                $debugLines[] = $parsed;
            }
        }

        return [
            'items' => $items,
            'debug' => $debug ? $debugLines : null,
        ];
    }

    // ---------- PARSEREK ----------

    private function parseHd(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bhd\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'HD';

        // FCI: HD A, B, C, D, E
        if (preg_match('/hd\s*([a-e])\b/i', $clean, $m)) {
            $parsed['value'] = strtoupper($m[1]);
        }

        // OFA szöveges ? FCI ekvivalens
        $ofaMap = [
            'excellent'  => 'A',
            'good'       => 'A',
            'fair'       => 'B',
            'borderline' => 'C',
            'mild'       => 'D',
            'moderate'   => 'D',
            'severe'     => 'E',
        ];
        foreach ($ofaMap as $k => $v) {
            if (str_contains($clean, $k)) {
                $parsed['value'] = $v;
            }
        }
    }

    private function parseEd(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bed\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'ED';

        if (preg_match('/ed\s*([0-3]\/[0-3])/i', $clean, $m)) {
            $parsed['value'] = $m[1];
        }
    }

    private function parseDm(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bdm\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'DM';

        $map = [
            'n\/n'     => 'CLEAR',
            'n\/dm'    => 'CARRIER',
            'dm\/dm'   => 'AFFECTED',
            'clear'    => 'CLEAR',
            'carrier'  => 'CARRIER',
            'affected' => 'AFFECTED',
        ];

        foreach ($map as $pattern => $canonical) {
            if (preg_match("/$pattern/i", $clean)) {
                $parsed['value'] = $canonical;
                break;
            }
        }
    }

    private function parseCea(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bcea\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'CEA';

        $map = [
            'clear'    => 'CLEAR',
            'carrier'  => 'CARRIER',
            'affected' => 'AFFECTED',
        ];

        foreach ($map as $k => $v) {
            if (str_contains($clean, $k)) {
                $parsed['value'] = $v;
            }
        }
    }

    private function parsePra(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bpra\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'PRA';

        $map = [
            'clear'    => 'CLEAR',
            'carrier'  => 'CARRIER',
            'affected' => 'AFFECTED',
        ];

        foreach ($map as $k => $v) {
            if (str_contains($clean, $k)) {
                $parsed['value'] = $v;
            }
        }
    }

    private function parseMdr1(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bmdr1\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'MDR1';

        $map = [
            'n\/n' => 'CLEAR',
            'n\/m' => 'CARRIER',
            'm\/m' => 'AFFECTED',
        ];

        foreach ($map as $pattern => $canonical) {
            if (preg_match("/$pattern/i", $clean)) {
                $parsed['value'] = $canonical;
                break;
            }
        }
    }

    private function parseHsf4(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bhsf4\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'HSF4';

        $map = [
            'n\/n' => 'CLEAR',
            'n\/m' => 'CARRIER',
            'm\/m' => 'AFFECTED',
        ];

        foreach ($map as $pattern => $canonical) {
            if (preg_match("/$pattern/i", $clean)) {
                $parsed['value'] = $canonical;
                break;
            }
        }
    }

    private function parseIvdd(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bivdd\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'IVDD';

        if (preg_match('/grade\s*([1-5])/i', $clean, $m)) {
            $parsed['value'] = 'GRADE ' . $m[1];
        }
    }

    private function parseBaer(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bbaer\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'BAER';

        if (str_contains($clean, 'bilateral')) {
            $parsed['value'] = 'BILATERAL NORMAL';
        } elseif (str_contains($clean, 'unilateral')) {
            $parsed['value'] = 'UNILATERAL NORMAL';
        } elseif (str_contains($clean, 'deaf')) {
            $parsed['value'] = 'DEAF';
        }
    }

    private function parseEcvo(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\becvo\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'ECVO';

        if (str_contains($clean, 'clear')) {
            $parsed['value'] = 'CLEAR';
        }
    }

    private function parseHeart(string $original, string $clean, array &$parsed): void
    {
        if (!preg_match('/\bheart\b/i', $original)) {
            return;
        }

        $parsed['type'] = 'HEART';

        if (preg_match('/grade\s*([1-6])/i', $clean, $m)) {
            $parsed['value'] = 'GRADE ' . $m[1];
        }
    }

    private function parseDate(string $original, array &$parsed): void
    {
        if ($parsed['date']) {
            return;
        }

        if (preg_match('/(20\d{2})/', $original, $m)) {
            $parsed['date'] = $m[1] . '-01-01';
        }
    }

    private function detectLab(string $clean): ?string
    {
        $labs = [
            'ofa'  => 'OFA',
            'fci'  => 'FCI',
            'bva'  => 'BVA',
            ' sv ' => 'SV',
            'ökv'  => 'ÖKV',
            'okv'  => 'ÖKV',
            'cmku' => 'CMKU',
            'skj'  => 'SKJ',
            'jkc'  => 'JKC',
            'ckc'  => 'CKC',
            'ankc' => 'ANKC',
            'nzkc' => 'NZKC',
        ];

        foreach ($labs as $k => $v) {
            if (str_contains($clean, $k)) {
                return $v;
            }
        }

        return null;
    }
}