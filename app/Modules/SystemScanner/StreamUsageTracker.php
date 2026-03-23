<?php

namespace App\Modules\SystemScanner;

use Illuminate\Support\Facades\DB;

class StreamUsageTracker
{
    public function __construct()
    {
        DB::connection('sqlite')->statement("
            CREATE TABLE IF NOT EXISTS usage (
                file TEXT PRIMARY KEY,
                hits INTEGER DEFAULT 0
            )
        ");
    }

    public function hit(string $file): void
    {
        DB::connection('sqlite')->statement("
            INSERT INTO usage (file, hits)
            VALUES (?, 1)
            ON CONFLICT(file) DO UPDATE SET hits = hits + 1
        ", [$file]);
    }

    public function getAll(): array
    {
        return DB::connection('sqlite')->table('usage')->pluck('hits', 'file')->toArray();
    }
}
