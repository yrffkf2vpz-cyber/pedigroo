<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class LocationNormalizer
{
    /**
     * Get or create location_id based on city + venue.
     */
    public static function id(string $city = null, string $venue = null): int
    {
        $cityClean  = self::clean($city);
        $venueClean = self::clean($venue);

        // Try to find existing location
        $existing = DB::table("locations")
            ->where("city_normalized", $cityClean)
            ->where("venue_normalized", $venueClean)
            ->value("id");

        if ($existing) {
            return $existing;
        }

        // Create new location
        return DB::table("locations")->insertGetId([
            "city"            => $city,
            "venue"           => $venue,
            "city_normalized" => $cityClean,
            "venue_normalized"=> $venueClean,
            "created_at"      => now(),
            "updated_at"      => now()
        ]);
    }

    /**
     * Normalize city/venue names for matching.
     */
    protected static function clean(?string $value): string
    {
        if (!$value) {
            return "";
        }

        $value = trim($value);
        $value = mb_strtolower($value);

        // Remove punctuation
        $value = str_replace([",", ".", "(", ")", "-"], " ", $value);

        // Collapse multiple spaces
        $value = preg_replace("/\s+/", " ", $value);

        return $value;
    }
}