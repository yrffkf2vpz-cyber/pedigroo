<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Club\EntryService;

class EntryController extends Controller
{
    public function __construct(
        protected EntryService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $eventId, Request $request)
    {
        try {
            $entries = $this->service->getForEvent($eventId);

            Log::info('Event entries viewed', [
                'user_id'  => $request->user()?->id,
                'event_id' => $eventId,
            ]);

            return view('admin.events.entries', [
                'entries' => $entries,
                'eventId' => $eventId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Event entries load failed', [
                'user_id'  => $request->user()?->id,
                'event_id' => $eventId,
                'error'    => $e->getMessage(),
            ]);

            return view('admin.events.entries', [
                'entries' => [],
                'eventId' => $eventId,
                'error'   => 'A nevezések betöltése sikertelen.',
            ]);
        }
    }
}
