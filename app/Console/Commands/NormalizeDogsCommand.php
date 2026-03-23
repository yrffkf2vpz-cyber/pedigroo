<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Services\Normalizers\NormalizePipelineService;
use App\Services\Ingest\SandboxDogSaver;
use App\Services\Ingest\DogRecordSaver;

class NormalizeDogsCommand extends Command
{
    protected $signature = 'pedroo:normalize-dogs {--debug} {--chunk=500}';
    protected $description = 'Run full ingest → normalize → sandbox → final pipeline on all pedroo_dogs records';

    public function handle(): int
    {
        $debug  = $this->option('debug') ?? false;
        $chunk  = (int)$this->option('chunk') ?: 500;

        $this->info("NormalizeDogsCommand started...");
        $this->info("Chunk size: {$chunk}");

        // Pipeline szolgáltatások
        $pipeline      = app(NormalizePipelineService::class);
        $sandboxSaver  = app(SandboxDogSaver::class);
        $finalSaver    = app(DogRecordSaver::class);

        // Összes kutya száma
        $total = DB::table('pedroo_dogs')->count();
        $this->info("Found {$total} dogs to normalize.");

        if ($total === 0) {
            $this->warn("No dogs found.");
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // CHUNKOLT FELDOLGOZÁS
        DB::table('pedroo_dogs')
            ->orderBy('id')
            ->chunk($chunk, function ($dogs) use ($pipeline, $sandboxSaver, $finalSaver, $debug, $bar) {

                foreach ($dogs as $dog) {

                    try {
                        // 1) Normalize pipeline
                        $result = $pipeline->process((array)$dog, $debug);

                        // 2) Sandbox mentés
                        $sandboxDog = $sandboxSaver->save($result['dog']);

                        // 3) Final mentés
                        $finalDog = $finalSaver->save($result['dog']);

                    } catch (\Throwable $e) {
                        // Hiba esetén nem áll meg a pipeline
                        $this->error("\n[ERROR] Dog ID {$dog->id}: {$e->getMessage()}");
                        continue;
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("NormalizeDogsCommand finished.");

        return Command::SUCCESS;
    }
}