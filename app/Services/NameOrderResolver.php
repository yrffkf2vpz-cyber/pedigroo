<?php

namespace App\Services;

use App\Models\PedrooDog;
use App\Enums\NameOrder;

class NameOrderResolver
{
    public function resolve(PedrooDog $dog): ?int
    {
        // 1) Tulajdonos minta
        if ($dog->owner && $dog->owner->name_order_id) {
            return $dog->owner->name_order_id;
        }

        // 2) Ország minta
        if ($dog->source_country) {
            $pattern = $this->patternForCountry($dog->source_country);
            if ($pattern) {
                return $pattern;
            }
        }

        // 3) Kennelnév felismerés
        $pattern = $this->detectKennelPattern($dog->real_name);
        if ($pattern) {
            return $pattern;
        }

        // 4) Nem egyértelmu ? admin review
        $dog->needs_review = 1;
        $dog->save();

        return null;
    }

    private function patternForCountry(string $country): ?int
    {
        return match (strtoupper($country)) {
            'HU', 'SK', 'RO' => NameOrder::REGISTERED_FIRST,
            'CZ', 'PL', 'DE' => NameOrder::CALL_FIRST,
            'FI'             => NameOrder::PREFIX_CALL_SUFFIX,
            default          => null,
        };
    }

    private function detectKennelPattern(string $name): ?int
    {
        $parts = explode(' ', $name);

        // suffix kennel (Lucifer Pörgelóci)
        if ($this->looksLikeKennel(end($parts))) {
            return NameOrder::CALL_FIRST;
        }

        // prefix kennel (Pörgelóci Lucifer)
        if ($this->looksLikeKennel($parts[0])) {
            return NameOrder::REGISTERED_FIRST;
        }

        return null;
    }

    private function looksLikeKennel(string $word): bool
    {
        return preg_match('/(i|sky|ski|cki|czy|ová|ský)$/ui', $word);
    }
}
