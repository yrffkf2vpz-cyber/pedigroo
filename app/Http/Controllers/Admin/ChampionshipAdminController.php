<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChampionshipAdminController extends Controller
{
    /**
     * List championships with filters.
     */
    public function index(Request $request)
    {
        $query = DB::table('pd_championships')
            ->orderBy('date', 'desc')
            ->orderBy('dog_id');

        if ($request->filled('dog_id')) {
            $query->where('dog_id', $request->dog_id);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('title_definition_id')) {
            $query->where('title_definition_id', $request->title_definition_id);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    /**
     * Create or update a championship record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dog_id'             => 'required|integer',
            'event_id'           => 'nullable|integer',
            'title_definition_id'=> 'required|integer',
            'country_id'         => 'nullable|integer',
            'date'               => 'nullable|date',
            'source'             => 'nullable|string|max:100',
            'external_id'        => 'nullable|string|max:191',
        ]);

        // DUPLIKÁCIÓ ELLENŐRZÉS
        $existing = DB::table('pd_championships')
            ->where('dog_id', $validated['dog_id'])
            ->where('title_definition_id', $validated['title_definition_id'])
            ->where('date', $validated['date'])
            ->value('id');

        if ($existing) {
            DB::table('pd_championships')
                ->where('id', $existing)
                ->update([
                    'event_id'    => $validated['event_id'] ?? null,
                    'country_id'  => $validated['country_id'] ?? null,
                    'source'      => $validated['source'] ?? null,
                    'external_id' => $validated['external_id'] ?? null,
                    'updated_at'  => now(),
                ]);

            return response()->json([
                'status' => 'updated',
                'id'     => $existing,
            ]);
        }

        // INSERT
        $id = DB::table('pd_championships')->insertGetId([
            'dog_id'             => $validated['dog_id'],
            'event_id'           => $validated['event_id'] ?? null,
            'title_definition_id'=> $validated['title_definition_id'],
            'country_id'         => $validated['country_id'] ?? null,
            'date'               => $validated['date'] ?? null,
            'source'             => $validated['source'] ?? null,
            'external_id'        => $validated['external_id'] ?? null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return response()->json([
            'status' => 'inserted',
            'id'     => $id,
        ]);
    }

    /**
     * Get a single championship record.
     */
    public function show(int $id)
    {
        $item = DB::table('pd_championships')->where('id', $id)->first();

        if (!$item) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json(['data' => $item]);
    }

    /**
     * Delete a championship record.
     */
    public function destroy(int $id)
    {
        DB::table('pd_championships')->where('id', $id)->delete();

        return response()->json(['status' => 'deleted']);
    }
}