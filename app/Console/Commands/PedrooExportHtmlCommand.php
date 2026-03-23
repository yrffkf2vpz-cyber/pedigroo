<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Pedroo\PedrooHtmlExportService;

class PedrooExportHtmlCommand extends Command
{
    protected $signature = 'pedroo:export-html 
                            {--category= : Csak egy kategóriát exportál (pl. services, dto, pipelines)}';

    protected $description = 'Pedroo – HTML fájl export a tanulási rendszerhez';

    public function handle(PedrooHtmlExportService $exporter)
    {
        $category = $this->option('category');

        $this->info("=== Pedroo HTML Export Engine ===");

        if ($category) {
            $this->info("Kategória exportálása: {$category}");
            $exporter->runCategory($category);
        } else {
            $this->info("Összes kategória exportálása…");
            $exporter->run();
        }

        $this->info("Kész. A Pedroo HTML-ek elérhetok a public/pedroo-review mappában.");
        return Command::SUCCESS;
    }
}