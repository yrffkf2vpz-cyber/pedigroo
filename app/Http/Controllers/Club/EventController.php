<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Club\EventService;

class EventController extends Controller
{
    public function __construct(
        protected EventService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $events = $this->service->all();

            Log::info('Events list viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.events.index', [
                'events' => $events,
            ]);
        } catch (\Throwable $e) {
            Log::error('Events list load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.events.index', [
                'events' => [],
                'error'  => 'Az események betöltése sikertelen.',
            ]);
        }
    }

    public function show(int $eventId, Request $request)
    {
        try {
            $event = $this->service->get($eventId);

            Log::info('Event viewed', [
                'user_id'  => $request->user()?->id,
                'event_id' => $eventId,
            ]);

            return view('admin.events.show', [
                'event' => $event,
            ]);
        } catch (\Throwable $e) {
            Log::error('Event load failed', [
                'user_id'  => $request->user()?->id,
                'event_id' => $eventId,
                'error'    => $e->getMessage(),
            ]);

            return view('admin.events.show', [
                'event' => null,
                'error' => 'Az esemény betöltése sikertelen.',
            ]);
        }
    }
}
