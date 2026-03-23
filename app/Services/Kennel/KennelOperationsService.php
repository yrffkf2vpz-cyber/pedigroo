<?php

namespace App\Services\Kennel;

use Illuminate\Support\Facades\DB;

class KennelOperationsService
{
    public function getForKennel(int $kennelId): array
    {
        return DB::table('pd_kennel_operations')
            ->where('kennel_id', $kennelId)
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }
}
