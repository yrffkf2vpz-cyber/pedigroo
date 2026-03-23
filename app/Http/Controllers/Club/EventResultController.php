<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Club\EventResultService;

class EventResultController extends Controller
{
    public function __construct(
        protected EventResultService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $eventId, Request $request)
    {
        try {
            $results = $this->service->getForEvent($eventId);

            Log::info('Event results viewed', [
                'user_id'  => $request->user()?->id,
                'event_id' => $eventId,
            ]);

            return view('admin.events.results', [
                'results' => $results,
                'eventId' => $eventId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Event results load failed', [
                'user_id'  => $request->user()?->id,
                'event_id' => $eventId,
                'error'    => $e->getMessage(),
            ]);

            return view('admin.events.results', [
                'results' => [],
                'eventId' => $eventId,
                'error'   => 'Az eredmények betöltése sikertelen.',
            ]);
        }
    }
}
