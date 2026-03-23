<?php

namespace App\Services\Kennel;

use Illuminate\Support\Facades\DB;

class KennelComplianceService
{
    public function getForKennel(int $kennelId): array
    {
        return DB::table('pd_kennel_compliance')
            ->where('kennel_id', $kennelId)
            ->orderBy('checked_at', 'desc')
            ->get()
            ->toArray();
    }
}
