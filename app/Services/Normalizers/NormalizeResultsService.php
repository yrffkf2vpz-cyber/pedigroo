<?php

namespace App\Services\Normalizers;

class NormalizeResultsService
{
    /**
     * Eredmények normalizálása (skeleton + debug)
     */
    public function normalize(array $raw, bool $debug = false): array
    {
        // Nyers input
        $rawResults = $raw['raw_results'] ?? [];

        // Egyelőre csak átadjuk a nyers sorokat (később: show név, dátum, eredmény típus, pontok)
        $items = [];

        foreach ($rawResults as $line) {
            $items[] = [
                'raw' => $line,
                // később: 'show' => ..., 'date' => ..., 'result' => ...
            ];
        }

        // Debug struktúra
        $debugData = $debug ? [
            'raw_input' => $rawResults,
            'count'     => count($rawResults),
            'parsed'    => $items,
            'notes'     => 'Ez csak skeleton. Később: show név, dátum, eredmény típus, CAC/CACIB/BOB felismerés.'
        ] : null;

        return [
            'items' => $items,
            'debug' => $debugData,
        ];
    }
}