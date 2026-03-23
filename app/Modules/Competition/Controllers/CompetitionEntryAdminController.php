<?php

namespace App\Modules\Competition\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Competition\Models\CompetitionEntry;

class CompetitionEntryAdminController extends Controller
{
    public function index()
    {
        return CompetitionEntry::with(['competition', 'user'])
            ->orderByDesc('id')
            ->get();
    }

    public function destroy(CompetitionEntry $entry)
    {
        $entry->delete();

        return response()->json(['status' => 'ok']);
    }
}
