<?php

namespace App\Services\Normalizers\RegNo;

class BreedCodeDetector
{
    protected array $modernHu = [
        'Pl.' => 'Puli',
        'Pm.' => 'Pumi',
        'Mu.' => 'Mudi',
        'Ku.' => 'Kuvasz',
        'Ko.' => 'Komondor',
    ];

    public function detect(string $raw, ?string $org = null, array $context = []): ?array
    {
        // 1) MET – fajtakód a reg_no-ban
        if ($org === 'MET') {
            foreach ($this->modernHu as $code => $name) {
                if (str_contains($raw, $code)) {
                    return [
                        'code'       => $code,
                        'name'       => $name,
                        'confidence' => 0.99,
                    ];
                }
            }
        }

        // 2) ha nincs fajtakód → környezetből (pl. pumi kennel oldal)
        if (!empty($context['breed_name'])) {
            return [
                'code'       => null,
                'name'       => $context['breed_name'],
                'confidence' => $context['breed_confidence'] ?? 0.9,
            ];
        }

        return null;
    }
}