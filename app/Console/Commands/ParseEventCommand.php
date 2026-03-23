<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Events\EventParserService;

class ParseEventCommand extends Command
{
    protected $signature = 'events:parse 
                            {parser : Parser type (mksz_html, generic_pdf, csv)} 
                            {path : Path to the source file}';

    protected $description = 'Parse a dog show event file (HTML, PDF, CSV) and output structured data';

    protected EventParserService $eventParserService;

    public function __construct(EventParserService $eventParserService)
    {
        parent::__construct();
        $this->eventParserService = $eventParserService;
    }

    public function handle(): int
    {
        $parser = $this->argument('parser');
        $path   = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $this->info("Parsing event file with parser [{$parser}] from [{$path}]...");

        try {
            $result = $this->eventParserService->parse($parser, $path, 'cli');

            $this->info('Parsing finished. Summary:');
            $this->line('Event name: ' . ($result['event_name'] ?? 'N/A'));
            $this->line('Date      : ' . ($result['date'] ?? 'N/A'));
            $this->line('Country   : ' . ($result['country'] ?? 'N/A'));
            $this->line('City      : ' . ($result['city'] ?? 'N/A'));
            $this->line('Location  : ' . ($result['location'] ?? 'N/A'));
            $this->line('Club      : ' . ($result['club'] ?? 'N/A'));
            $this->line('Judges    : ' . count($result['judges'] ?? []));
            $this->line('Rings     : ' . count($result['rings'] ?? []));
            $this->line('Results   : ' . count($result['results'] ?? []));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error during parsing: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}