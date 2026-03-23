<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class EventNormalizer
{
    /**
     * Normalize event metadata and return sandbox event_id.
     */
    public static function id(array $parsed): int
    {
        // Clean fields
        $name     = self::cleanName($parsed['event_name'] ?? 'Unknown Event');
        $country  = self::cleanCountry($parsed['country'] ?? null);
        $city     = self::cleanCity($parsed['city'] ?? null);
        $venue    = self::cleanVenue($parsed['location'] ?? null);
        $date     = $parsed['date'] ?? null;

        // Hash for deduplication
        $hash = sha1(json_encode([
            $name, $country, $city, $venue, $date
        ]));

        // 1) Try to find existing sandbox event
        $existing = DB::table('pedroo_events')
            ->where('hash', $hash)
            ->value('id');

        if ($existing) {
            return $existing;
        }

        // 2) Insert new sandbox event
        return DB::table('pedroo_events')->insertGetId([
            'source'      => $parsed['source'] ?? 'parser',
            'external_id' => null,
            'name'        => $name,
            'country'     => $country,
            'city'        => $city,
            'venue'       => $venue,
            'start_date'  => $date,
            'end_date'    => $date,
            'event_type'  => $parsed['event_type'] ?? null,
            'raw'         => json_encode($parsed),
            'hash'        => $hash,
            'confidence'  => 90,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /* ---------------------------------------------------------
     *  CLEANERS
     * --------------------------------------------------------- */

    protected static function cleanName(?string $name): ?string
    {
        if (!$name) return null;
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        return ucfirst($name);
    }

    protected static function cleanCountry(?string $country): ?string
    {
        if (!$country) return null;
        return ucfirst(strtolower(trim($country)));
    }

    protected static function cleanCity(?string $city): ?string
    {
        if (!$city) return null;
        return ucfirst(strtolower(trim($city)));
    }

    protected static function cleanVenue(?string $venue): ?string
    {
        if (!$venue) return null;
        return trim($venue);
    }
}