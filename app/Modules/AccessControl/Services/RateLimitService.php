<?php

namespace App\Modules\AccessControl\Services;

use Illuminate\Support\Facades\DB;

class RateLimitService
{
    public function isLocked(string $action, int $userId): bool
    {
        $row = DB::table('pd_user_rate_limits')
            ->where('user_id', $userId)
            ->where('action', $action)
            ->first();

        return $row && $row->locked_until && now()->lt($row->locked_until);
    }

    public function increment(string $action, int $userId): int
    {
        $row = DB::table('pd_user_rate_limits')
            ->where('user_id', $userId)
            ->where('action', $action)
            ->first();

        if (!$row) {
            DB::table('pd_user_rate_limits')->insert([
                'user_id'      => $userId,
                'action'       => $action,
                'attempts'     => 1,
                'locked_until' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return 1;
        }

        DB::table('pd_user_rate_limits')
            ->where('id', $row->id)
            ->increment('attempts');

        return $row->attempts + 1;
    }

    public function lock(string $action, int $userId, int $minutes): void
    {
        DB::table('pd_user_rate_limits')
            ->where('user_id', $userId)
            ->where('action', $action)
            ->update([
                'locked_until' => now()->addMinutes($minutes),
                'updated_at'   => now(),
            ]);
    }

    public function reset(string $action, int $userId): void
    {
        DB::table('pd_user_rate_limits')
            ->where('user_id', $userId)
            ->where('action', $action)
            ->update([
                'attempts'     => 0,
                'locked_until' => null,
                'updated_at'   => now(),
            ]);
    }
}
