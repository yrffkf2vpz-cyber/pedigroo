<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\SystemScanner\SelfScannerRunner;

class PedrooSelfScanCommand extends Command
{
    protected $signature = 'pedroo:self-scan';
    protected $description = 'Futtatja a Pedroo önvizsgálati MRI rendszerét és tömör JSON jelentést készít.';

    public function handle()
    {
        $this->info("?? Pedroo MRI futtatása...");

        try {
            /** @var SelfScannerRunner $runner */
            $runner = app(SelfScannerRunner::class);

            $report = $runner->run();

            $path = storage_path('pedroo_system_report.json');

            file_put_contents(
                $path,
                json_encode($report, JSON_PRETTY_PRINT)
            );

            $this->info("? Jelentés elkészült.");
            $this->info("?? Mentve ide: {$path}");
            $this->info("?? Másold ki a JSON-t és illeszd be nekem.");

        } catch (\Throwable $e) {
            $this->error("? Hiba történt a Self-Scan futtatása közben:");
            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
