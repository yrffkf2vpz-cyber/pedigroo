<?php

namespace App\Services\Club;

use Illuminate\Support\Facades\DB;

class EventService
{
    public function all(): array
    {
        return DB::table('pd_events')
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    public function get(int $eventId): ?object
    {
        return DB::table('pd_events')
            ->where('id', $eventId)
            ->first();
    }
}
