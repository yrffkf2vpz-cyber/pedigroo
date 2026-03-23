<?php

namespace App\Services\Club;

use Illuminate\Support\Facades\DB;

class TitleService
{
    public function all(): array
    {
        return DB::table('title_definitions')
            ->orderBy('title_code')
            ->get()
            ->toArray();
    }
}
