<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class ComplianceService
{
    public function all(): array
    {
        return DB::table('pd_compliance_items')
            ->orderBy('checked_at', 'desc')
            ->get()
            ->toArray();
    }
}
