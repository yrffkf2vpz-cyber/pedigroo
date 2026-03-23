<?php

namespace App\Modules\SystemScanner;

class FileUsageTracker
{
    public function hit(string $file): void
    {
        app(SystemScannerService::class)->trackUsage($file);
    }
}
