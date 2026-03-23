<?php

namespace App\Modules\AccessControl\Services;

use App\Models\Access\AccessRequest;

class SuspiciousActivityService
{
    public function detect(int $userId): bool
    {
        // 1) túl sok kérelem 5 perc alatt
        $recent = AccessRequest::where('requester_user_id', $userId)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recent >= 10) {
            return true;
        }

        // 2) ismételt kérések ugyanarra a kutyára
        $duplicates = AccessRequest::where('requester_user_id', $userId)
            ->select('dog_id', 'request_type')
            ->groupBy('dog_id', 'request_type')
            ->havingRaw('COUNT(*) >= 5')
            ->exists();

        if ($duplicates) {
            return true;
        }

        return false;
    }
}