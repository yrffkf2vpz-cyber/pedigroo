<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Title\TitleDefinitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TitleAdminController extends Controller
{
    protected TitleDefinitionService $definitions;

    public function __construct(TitleDefinitionService $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * List all titles (with optional filters).
     */
    public function index(Request $request)
    {
        $query = DB::table('title_definitions')
            ->orderBy('global_id')
            ->orderBy('country_id')
            ->orderBy('title_code');

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('global_id')) {
            $query->where('global_id', $request->global_id);
        }

        if ($request->filled('title_code')) {
            $query->where('title_code', 'LIKE', '%' . $request->title_code . '%');
        }

        if ($request->filled('title_name')) {
            $query->where('title_name', 'LIKE', '%' . $request->title_name . '%');
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    /**
     * Create or update a title definition.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'global_id'   => 'required|integer',
            'country_id'  => 'required|integer',
            'title_code'  => 'required|string|max:255',
            'title_name'  => 'nullable|string|max:255',
            'requirement' => 'nullable|string',
        ]);

        $id = $this->definitions->upsert($validated);

        return response()->json([
            'status' => 'ok',
            'id'     => $id,
        ]);
    }

    /**
     * Get a single title definition.
     */
    public function show(int $id)
    {
        $item = DB::table('title_definitions')->where('id', $id)->first();

        if (!$item) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json(['data' => $item]);
    }

    /**
     * Delete a title definition.
     */
    public function destroy(int $id)
    {
        DB::table('title_definitions')->where('id', $id)->delete();

        return response()->json(['status' => 'deleted']);
    }
}