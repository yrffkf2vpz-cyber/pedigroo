<?php

namespace App\Services\AI;

use Illuminate\Support\Collection;

class RuleGeneratorService
{
    /**
     * Szabályjavaslatok generálása a minták alapján.
     */
    public function generate(Collection $patterns): Collection
    {
        return $patterns->map(function ($pattern) {
            $type = $pattern->detected_type;
            $value = $pattern->raw_value;

            return [
                'type' => $type,
                'raw_value' => $value,
                'suggested_rule' => $this->suggestRule($type, $value),
                'occurrences' => $pattern->occurrences,
            ];
        });
    }

    /**
     * Egyszeru szabályjavaslatok az elso verzióban.
     */
    private function suggestRule(string $type, string $value): string
    {
        // UNKNOWN_COLOR
        if ($type === 'UNKNOWN_COLOR') {
            return "Szín normalizálása: '" . $value . "' ? '" . ucfirst(strtolower($value)) . "'";
        }

        // UNKNOWN_COUNTRY
        if ($type === 'UNKNOWN_COUNTRY') {
            return "Országkód javítása: '" . $value . "' ? '??' (ellenorizni kell)";
        }

        // UNKNOWN_REG_NO
        if ($type === 'UNKNOWN_REG_NO') {
            return "Regisztrációs szám formátum ellenorzése: '" . $value . "'";
        }

        // UNKNOWN_HEALTH
        if ($type === 'UNKNOWN_HEALTH') {
            return "Egészségadat tisztítása: '" . $value . "'";
        }

        return "Nincs javaslat.";
    }
}