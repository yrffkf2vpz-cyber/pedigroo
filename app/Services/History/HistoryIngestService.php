<?php

namespace App\Services\History;

use Illuminate\Support\Facades\DB;

class HistoryIngestService
{
    /**
     * Ingest a list of historical events.
     *
     * @param array $events
     * @return void
     */
    public function ingest(array $events): void
    {
        foreach ($events as $event) {
            $this->storeEvent($event);
        }
    }

    /**
     * Store a single historical event.
     *
     * @param array $event
     * @return void
     */
    protected function storeEvent(array $event): void
    {
        // 1) Kötelező mezők ellenőrzése
        if (!isset($event['type']) || !isset($event['title_key'])) {
            throw new \Exception("History event missing required fields: type or title_key");
        }

        // 2) Params JSON automatikus generálása
        $params = $this->generateParams($event);

        // 3) Mentés SQL-be
        DB::table('history_events')->insert([
            'type'            => $event['type'],
            'scope'           => $event['scope'] ?? null,
            'code'            => $event['code'] ?? null,
            'breed'           => $event['breed'] ?? null,
            'registry'        => $event['registry'] ?? null,
            'year'            => $event['year'] ?? null,
            'date'            => $event['date'] ?? null,

            'title_key'       => $event['title_key'],
            'description_key' => $event['description_key'] ?? null,

            'params'          => json_encode($params, JSON_UNESCAPED_UNICODE),

            'value_before'    => $event['value_before'] ?? null,
            'value_after'     => $event['value_after'] ?? null,

            'meta'            => isset($event['meta']) ? json_encode($event['meta'], JSON_UNESCAPED_UNICODE) : null,

            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    /**
     * Generate params JSON automatically based on event type.
     *
     * @param array $event
     * @return array
     */
    protected function generateParams(array $event): array
    {
        $params = [];

        switch ($event['type']) {

            case 'country_code_change':
                $params = [
                    'country' => $event['country'] ?? null,
                    'from'    => $event['value_before'] ?? null,
                    'to'      => $event['value_after'] ?? null,
                    'year'    => $event['year'] ?? null,
                ];
                break;

            case 'prefix_change':
                $params = [
                    'registry'       => $event['registry'] ?? null,
                    'prefix_before'  => $event['value_before'] ?? null,
                    'prefix_after'   => $event['value_after'] ?? null,
                    'year'           => $event['year'] ?? null,
                ];
                break;

            case 'breed_standard_change':
                $params = [
                    'breed'   => $event['breed'] ?? null,
                    'section' => $event['section'] ?? null,
                    'year'    => $event['year'] ?? null,
                ];
                break;

            default:
                // fallback: minden mezőt átadunk
                $params = $event;
                break;
        }

        return $params;
    }
}