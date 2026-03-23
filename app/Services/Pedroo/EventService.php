<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class EventService
{
    /**
     * Pedroo event feldolgozás
     *
     * @param  object  $parsedEvent
     *  Kötelező mezők:
     *    - name
     *    - event_type_id
     *    - country_id
     *    - start_date VAGY end_date
     *
     *  Opcionális mezők:
     *    - notes
     *    - city
     *    - external_id
     *    - source
     *
     * @return int event_id
     */
    public function processEvent(object $parsedEvent): int
    {
        $name        = trim($parsedEvent->name ?? '');
        $eventTypeId = $parsedEvent->event_type_id ?? null;
        $startDate   = $parsedEvent->start_date ?? null;
        $endDate     = $parsedEvent->end_date ?? null;
        $notes       = $parsedEvent->notes ?? null;
        $countryId   = $parsedEvent->country_id ?? null;
        $city        = $parsedEvent->city ?? null;
        $externalId  = $parsedEvent->external_id ?? null;
        $source      = $parsedEvent->source ?? 'pedroo';

        // minimális védelem – ha nincs név vagy event_type vagy country, inkább ne írjunk
        if (!$name || !$eventTypeId || !$countryId) {
            throw new \InvalidArgumentException('Hiányos event adatok (name, event_type_id, country_id kötelező).');
        }

        // ha nincs start_date, de van end_date, használjuk azt
        if (!$startDate && $endDate) {
            $startDate = $endDate;
        }

        // HASH – duplikáció elleni védelem
        $hash = hash('sha256', strtolower(
            $name . '|' . ($startDate ?? '') . '|' . ($city ?? '') . '|' . $countryId . '|' . $source
        ));

        // 1) KERESÉS HASH ALAPJÁN
        $event = DB::table('events')
            ->where('hash', $hash)
            ->first();

        // 2) KERESÉS external_id + source ALAPJÁN (ha van external_id)
        if (!$event && $externalId) {
            $event = DB::table('events')
                ->where('external_id', $externalId)
                ->where('source', $source)
                ->first();
        }

        // 3) HA NINCS → BESZÚRÁS
        if (!$event) {
            return DB::table('events')->insertGetId([
                'name'         => $name,
                'event_type_id'=> $eventTypeId,
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'notes'        => $notes,
                'country_id'   => $countryId,
                'city'         => $city,
                'date'         => $startDate ?? $endDate,
                'hash'         => $hash,
                'external_id'  => $externalId,
                'source'       => $source,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // 4) HA VAN → OKOS FRISSÍTÉS
        DB::table('events')
            ->where('id', $event->id)
            ->update([
                // hosszabb név felülírhatja a rövidebbet
                'name'       => strlen($name) > strlen($event->name) ? $name : $event->name,
                'notes'      => $notes ?? $event->notes,
                'city'       => $city ?? $event->city,
                'start_date' => $startDate ?? $event->start_date,
                'end_date'   => $endDate ?? $event->end_date,
                'updated_at' => now(),
            ]);

        return (int) $event->id;
    }
}