<?php

namespace App\Services\Club;

use Illuminate\Support\Facades\DB;

class EventResultService
{
    public function getForEvent(int $eventId): array
    {
        return DB::table('pd_event_results')
            ->where('event_id', $eventId)
            ->orderBy('placement')
            ->get()
            ->toArray();
    }
}
