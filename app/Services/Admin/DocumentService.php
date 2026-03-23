<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class DocumentService
{
    public function all(): array
    {
        return DB::table('pd_documents')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
