<?php

namespace App\Services\Normalizers\RegNo;

class OrganizationDetector
{
    protected array $map = [
        'MET'    => ['layer' => 'modern',     'country' => 'HU'],
        'MEOE'   => ['layer' => 'historical', 'country' => 'HU'],
        'MEOESZ' => ['layer' => 'historical', 'country' => 'HU'],
        'OMKT'   => ['layer' => 'legacy',     'country' => 'HU'],
        // később: AKC, KC, LOF, VDH, ENCI, stb.
    ];

    public function detect(string $raw): ?array
    {
        $r = strtoupper($raw);

        foreach ($this->map as $prefix => $info) {
            if (str_starts_with($r, $prefix.'.') || str_starts_with($r, $prefix.' ')) {
                return [
                    'organization' => $prefix,
                    'layer'        => $info['layer'],
                    'country'      => $info['country'],
                ];
            }
        }

        return null;
    }
}