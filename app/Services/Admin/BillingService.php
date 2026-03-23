<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class BillingService
{
    public function all(): array
    {
        return DB::table('pd_invoices')
            ->orderBy('issued_at', 'desc')
            ->get()
            ->toArray();
    }
}
