<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class EventPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) DUPLIKÁCIÓ ELLENŐRZÉS PD OLDALON
            $existing = DB::table('pd_events')
                ->where('name', $sandbox->name)
                ->where('country', $sandbox->country)
                ->where('city', $sandbox->city)
                ->where('venue', $sandbox->venue)
                ->where('start_date', $sandbox->start_date)
                ->where('end_date', $sandbox->end_date)
                ->value('id');

            if ($existing) {

                DB::table('pd_events')
                    ->where('id', $existing)
                    ->update([
                        'event_type' => $sandbox->event_type,
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                // 2) INSERT PD OLDALRA
                $finalId = DB::table('pd_events')->insertGetId([
                    'name'       => $sandbox->name,
                    'country'    => $sandbox->country,
                    'city'       => $sandbox->city,
                    'venue'      => $sandbox->venue,
                    'start_date' => $sandbox->start_date,
                    'end_date'   => $sandbox->end_date,
                    'event_type' => $sandbox->event_type,
                    'organizer'  => $sandbox->source, // nincs organizer mező sandboxban → source a legjobb mapping
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3) SANDBOX AUDIT UPDATE
            DB::table('pedroo_events')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => "Promoted to pd_events (ID: {$finalId})",
                ]);

            return $finalId;
        });
    }
}