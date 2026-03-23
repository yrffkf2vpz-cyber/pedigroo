<?php

namespace App\Services\Kennel;

use Illuminate\Support\Facades\DB;

class KennelFacilityService
{
    public function get(int $kennelId): ?object
    {
        return DB::table('pd_kennel_facilities')
            ->where('kennel_id', $kennelId)
            ->first();
    }
}
