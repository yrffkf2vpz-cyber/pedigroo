<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class InsuranceService
{
    public function all(): array
    {
        return DB::table('pd_insurance_policies')
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();
    }
}
