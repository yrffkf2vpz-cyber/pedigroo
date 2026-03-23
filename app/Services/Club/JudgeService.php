<?php

namespace App\Services\Club;

use Illuminate\Support\Facades\DB;

class JudgeService
{
    public function all(): array
    {
        return DB::table('pd_judges')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
