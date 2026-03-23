<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * A parancsok manuális regisztrálása
     */
    protected $commands = [
        \App\Console\Commands\PedrooPipelineRunCommand::class,
        \App\Console\Commands\PedrooParseMasterPlanCommand::class,
        \App\Console\Commands\PedrooUtf8FixCommand::class,
        \App\Console\Commands\WebImportUrlCommand::class,
        \App\Console\Commands\PedrooExportCommand::class,

        // Shadow Backup parancsok
        \App\Console\Commands\ShadowBackup::class,
        \App\Console\Commands\ShadowRestore::class,
    ];

    /**
     * Ütemezett feladatok
     */
    protected function schedule(Schedule $schedule): void
    {
    // 10 percenként snapshot az app/ mappáról
    $schedule->command('pedroo:shadow-backup')->everySixHours();

    // Lejárt engedélyek automatikus kezelése
    $schedule->command('permissions:expire')->hourly();
    }

    /**
     * Parancsok betöltése
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}