<?php

namespace App\Services\Normalizers;

class PromotionNormalizer
{
    /**
     * Promotion normalizálás.
     * Később: eredményekből címek, rangok, kvalifikációk kinyerése.
     */
    public function normalize(array $results): array
    {
        // Minimális működő struktúra
        return [
            'promotions' => [],

            'debug' => [
                'input' => $results,
                'normalized' => [],
            ],
        ];
    }
}