<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\WebImporter\WebImporterService;

class WebImportUrlCommand extends Command
{
    protected $signature = 'pedroo:import-url {url}';
    protected $description = 'Import a dog and its pedigree from a given URL using the WebImporter';

    public function handle(WebImporterService $importer)
    {
        $url = $this->argument('url');

        $this->info("Downloading: $url");

        try {
            $response = Http::timeout(20)->get($url);
        } catch (\Exception $e) {
            $this->error("Failed to download URL: " . $e->getMessage());
            return Command::FAILURE;
        }

        if (!$response->successful()) {
            $this->error("HTTP error: " . $response->status());
            return Command::FAILURE;
        }

        $html = $response->body();

        $this->info("Processing HTML...");

        try {
            $dogId = $importer->importFromHtml($html);
        } catch (\Throwable $e) {
            $this->error("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("Import successful! Dog ID: $dogId");

        return Command::SUCCESS;
    }
}