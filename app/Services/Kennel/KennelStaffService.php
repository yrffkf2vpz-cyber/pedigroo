<?php

namespace App\Services\Kennel;

use Illuminate\Support\Facades\DB;

class KennelStaffService
{
    public function getForKennel(int $kennelId): array
    {
        return DB::table('pd_kennel_staff')
            ->where('kennel_id', $kennelId)
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
