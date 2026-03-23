<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\NormalizeDogJob;

class DispatchNormalizeDogs extends Command
{
    protected $signature = 'pedroo:dispatch-normalize {--chunk=500}';
    protected $description = 'Queue all pedroo_dogs for normalization';

    public function handle()
    {
        $chunk = (int)$this->option('chunk') ?: 500;

        $total = DB::table('pedroo_dogs')->count();
        $this->info("Dispatching {$total} dogs to queue...");

        DB::table('pedroo_dogs')
            ->orderBy('id')
            ->chunk($chunk, function ($dogs) {
                foreach ($dogs as $dog) {
                    NormalizeDogJob::dispatch($dog->id);
                }
            });

        $this->info("All dogs dispatched to queue.");
        return 0;
    }
}