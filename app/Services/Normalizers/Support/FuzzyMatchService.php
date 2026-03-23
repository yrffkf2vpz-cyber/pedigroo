<?php

namespace App\Services\Normalizers\Support;

use App\Models\PdDog;
use App\Models\Kennel;
use Illuminate\Support\Str;

class FuzzyMatchService
{
    /**
     * Szülo (parent) fuzzy match:
     * - név alapján
     * - opcionálisan reg_no + country alapján erosítés
     */
    public function matchParent(?string $name, ?string $regNo = null, ?string $country = null): ?array
    {
        if (!$name) {
            return null;
        }

        $cleanName = $this->normalizeText($name);

        // 1) Eros próbálkozás: név + reg_no
        if ($regNo) {
            $dog = PdDog::query()
                ->whereRaw('LOWER(name) = ?', [$cleanName])
                ->orWhere('reg_no_clean', $regNo)
                ->first();

            if ($dog) {
                return [
                    'dog_id'   => $dog->id,
                    'canonical'=> $dog->name,
                    'reg_no'   => $dog->reg_no_clean,
                    'country'  => $dog->origin_country,
                    'score'    => 0.95,
                ];
            }
        }

        // 2) Csak név alapján, LIKE + egyszeru hasonlóság
        $candidates = PdDog::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . $cleanName . '%'])
            ->limit(10)
            ->get();

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $dog) {
            $score = $this->similarity($cleanName, $this->normalizeText($dog->name));

            if ($country && $dog->origin_country === $country) {
                $score += 0.05;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $dog;
            }
        }

        if ($best && $bestScore >= 0.75) {
            return [
                'dog_id'   => $best->id,
                'canonical'=> $best->name,
                'reg_no'   => $best->reg_no_clean,
                'country'  => $best->origin_country,
                'score'    => $bestScore,
            ];
        }

        return null;
    }

    /**
     * Kennel fuzzy match:
     * - kennel név alapján
     */
    public function matchKennel(?string $kennelName): ?array
    {
        if (!$kennelName) {
            return null;
        }

        $clean = $this->normalizeText($kennelName);

        $candidates = Kennel::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . $clean . '%'])
            ->limit(10)
            ->get();

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $kennel) {
            $score = $this->similarity($clean, $this->normalizeText($kennel->name));

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $kennel;
            }
        }

        if ($best && $bestScore >= 0.75) {
            return [
                'kennel_id' => $best->id,
                'canonical' => $best->name,
                'score'     => $bestScore,
            ];
        }

        return null;
    }

    /**
     * Regisztrációs szám fuzzy match:
     * - prefix + number + year kombinációra
     * - vagy raw reg_no_clean mezore
     */
    public function matchRegNo(?string $rawRegNo): ?array
    {
        if (!$rawRegNo) {
            return null;
        }

        $clean = $this->normalizeText($rawRegNo);

        // 1) Pontos egyezés reg_no_clean mezore
        $dog = PdDog::query()
            ->where('reg_no_clean', $clean)
            ->first();

        if ($dog) {
            return [
                'dog_id'  => $dog->id,
                'prefix'  => null,
                'number'  => null,
                'year'    => null,
                'issuer'  => null,
                'country' => $dog->origin_country,
                'score'   => 0.95,
            ];
        }

        // 2) LIKE + hasonlóság
        $candidates = PdDog::query()
            ->where('reg_no_clean', 'LIKE', '%' . $clean . '%')
            ->limit(10)
            ->get();

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $candidate) {
            $score = $this->similarity($clean, $this->normalizeText($candidate->reg_no_clean));

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $candidate;
            }
        }

        if ($best && $bestScore >= 0.75) {
            return [
                'dog_id'  => $best->id,
                'prefix'  => null,
                'number'  => null,
                'year'    => null,
                'issuer'  => null,
                'country' => $best->origin_country,
                'score'   => $bestScore,
            ];
        }

        return null;
    }

    // -----------------------------------------------------
    // Segédfüggvények
    // -----------------------------------------------------

    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $text = strtr($text, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o',
            'ö' => 'o', 'o' => 'o', 'ú' => 'u', 'ü' => 'u', 'u' => 'u',
        ]);

        return preg_replace('/\s+/', ' ', $text);
    }

    /**
     * Egyszeru hasonlósági metrika 0–1 között.
     */
    protected function similarity(string $a, string $b): float
    {
        if ($a === $b) {
            return 1.0;
        }

        similar_text($a, $b, $percent);

        return $percent / 100;
    }
}