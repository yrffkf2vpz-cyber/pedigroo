<?php

namespace App\Http\Controllers\Timeline;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class BaseTimelineController extends Controller
{
    abstract protected function service();
    abstract protected function factory();
    abstract protected function model();

    public function index($id)
    {
        $items = $this->model()
            ->where($this->model()->getForeignKey(), $id)
            ->orderBy('timestamp', 'asc')
            ->get();

        return response()->json([
            'data' => $items
        ]);
    }

    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:100',
            'data' => 'nullable|array',
            'timestamp' => 'nullable|date',
        ]);

        $event = $this->service()->addEvent(
            $id,
            $validated['event_type'],
            $validated['data'] ?? [],
            $validated['timestamp'] ?? null
        );

        return response()->json([
            'data' => $event
        ]);
    }
}