<?php

namespace App\Services\Club;

use Illuminate\Support\Facades\DB;

class EntryService
{
    public function getForEvent(int $eventId): array
    {
        return DB::table('pd_event_entries')
            ->where('event_id', $eventId)
            ->orderBy('dog_name')
            ->get()
            ->toArray();
    }
}
