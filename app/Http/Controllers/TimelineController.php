<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogTimelineService;

class TimelineController extends Controller
{
    public function __construct(
        protected DogTimelineService $service
    ) {
        // csak admin vagy superadmin érheti el
        $this->middleware(['auth', 'can:admin']);
    }

    /**
     * Egy kutya teljes idővonala
     */
    public function show(int $dogId, Request $request)
    {
        try {
            $timeline = $this->service->getTimeline($dogId);

            Log::info('Dog timeline viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.timeline.show', [
                'timeline' => $timeline,
                'dogId'    => $dogId,
            ]);

        } catch (\Throwable $e) {

            Log::error('Dog timeline load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.timeline.show', [
                'timeline' => [],
                'dogId'    => $dogId,
                'error'    => 'Az idővonal betöltése sikertelen.',
            ]);
        }
    }
}
