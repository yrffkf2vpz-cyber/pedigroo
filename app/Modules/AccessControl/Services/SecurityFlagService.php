<?php

namespace App\Modules\AccessControl\Services;

use Illuminate\Support\Facades\DB;

class SecurityFlagService
{
    public function addFlag(int $userId, string $flag): void
    {
        DB::table('pd_user_security_flags')->insert([
            'user_id'    => $userId,
            'flag'       => $flag,
            'created_at' => now(),
        ]);
    }

    public function hasFlag(int $userId, string $flag): bool
    {
        return DB::table('pd_user_security_flags')
            ->where('user_id', $userId)
            ->where('flag', $flag)
            ->exists();
    }

    public function getFlags(int $userId): array
    {
        return DB::table('pd_user_security_flags')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->pluck('flag')
            ->toArray();
    }
}
