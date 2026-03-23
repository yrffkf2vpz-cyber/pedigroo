<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Events\EventsIngestService;

class IngestEvents extends Command
{
    protected $signature = 'events:ingest {--file=} {--path=events/normalized}';
    protected $description = 'Ingest normalized event JSON files into SQL';

    public function handle(EventsIngestService $service)
    {
        $path = $this->option('path');
        $file = $this->option('file');

        if ($file) {
            $this->info("Ingesting single file: {$file}");
            $service->ingestSingleFile($path.'/'.$file);
        } else {
            $this->info("Ingesting all files from: storage/app/{$path}");
            $service->ingestAllFromPath($path, $this);
        }

        $this->info('Events ingest finished.');
    }
}