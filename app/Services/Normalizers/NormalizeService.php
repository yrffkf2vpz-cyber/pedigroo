<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;
use App\Services\Health\HealthNormalizer;

class NormalizeService
{
    protected HealthNormalizer $health;

    public function __construct(HealthNormalizer $health)
    {
        $this->health = $health;
    }

    /**
     * Normalize country codes (FIN → FI, S → SE, etc.)
     */
    public function normalizeCountryCode(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $code = strtoupper(trim($raw));

        // 1) direkt, kézi mapping (gyors fallback)
        $map = [
            'FIN' => 'FI',
            'S'   => 'SE',
        ];
        if (isset($map[$code])) {
            return $map[$code];
        }

        // 2) history_events alapján (country_code_change)
        $event = DB::table('history_events')
            ->where('type', 'country_code_change')
            ->where(function ($q) use ($code) {
                $q->where('value_before', $code)
                  ->orWhere('value_after', $code);
            })
            ->orderBy('year', 'desc')
            ->first();

        if ($event && $event->value_after) {
            return $event->value_after;
        }

        return $code;
    }

    /**
     * Normalize registry prefixes (MET DSZMV → MET.Dszmv., etc.)
     */
    public function normalizeRegistryPrefix(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        // 1) alap tisztítás
        $clean = trim($raw);

        // 2) whitespace → pont
        // pl. "MET DSZMV" → "MET.DSZMV"
        $clean = preg_replace('/\s+/', '.', $clean);

        // 3) végpont hozzáadása, ha hiányzik
        if (!str_ends_with($clean, '.')) {
            $clean .= '.';
        }

        // 4) nagybetűsítés + speciális kisbetűk (MET.* esetek)
        // MET.DSZMV. → MET.Dszmv.
        if (stripos($clean, 'MET.DSZMV.') === 0 || stripos($clean, 'MET.Dszmv.') === 0) {
            $clean = 'MET.Dszmv.';
        }

        // 5) history_events alapján (prefix_change)
        $event = DB::table('history_events')
            ->where('type', 'prefix_change')
            ->where(function ($q) use ($clean) {
                $q->where('value_before', $clean)
                  ->orWhere('value_after', $clean);
            })
            ->orderBy('year', 'desc')
            ->first();

        if ($event && $event->value_after) {
            return $event->value_after;
        }

        return $clean;
    }

    /**
     * Normalize health data for a given dog
     */
    public function normalizeHealth(string $dogId): array
    {
        return $this->health->normalize($dogId);
    }
}