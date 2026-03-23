<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class EventPromotionNormalizer
{
    /**
     * Promote sandbox event_id into pd_events.
     * Returns final pd_events.id
     */
    public static function promote(int $eventId): int
    {
        // 1) Try to find an event with the same generated name
        $existing = DB::table('pd_events')
            ->where('name', 'Imported Event '.$eventId)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Create minimal event record
        return DB::table('pd_events')->insertGetId([
            'name'        => 'Imported Event '.$eventId,
            'country'     => null,
            'city'        => null,
            'venue'       => null,
            'start_date'  => null,
            'end_date'    => null,
            'event_type'  => null,
            'organizer'   => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}