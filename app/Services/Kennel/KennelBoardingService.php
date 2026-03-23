<?php

namespace App\Services\Kennel;

use Illuminate\Support\Facades\DB;

class KennelBoardingService
{
    public function getForKennel(int $kennelId): array
    {
        return DB::table('pd_boarding')
            ->where('kennel_id', $kennelId)
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();
    }
}
