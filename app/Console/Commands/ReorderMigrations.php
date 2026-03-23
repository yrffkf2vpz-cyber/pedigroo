<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReorderMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reorder-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $basePath = database_path('migrations');

    $order = [
        // 1. Alap
        'pd_countries',
        'pd_country_aliases',
        'pd_locations',
        'pd_groups',
        'pd_classes',
        'pd_breeds',

        // 2. Személyek / szervezetek
        'pd_owners',
        'pd_parents',
        'pd_kennels',

        // 3. Dogs
        'pd_dogs',

        // 4. Dog kapcsolatok
        'pd_children',
        'pd_dog_ancestry',
        'pd_dog_coi',
        'pd_dog_behavior_results',
        'pd_dog_event_results',
        'pd_dog_sport_results',
        'pd_dog_working_results',

        // 5. Egyéb
        'pd_events',
        'pd_event_results',
        'pd_rule_suggestions',
    ];

    $files = collect(scandir($basePath))
        ->filter(fn($f) => str_contains($f, 'create_pd_'))
        ->values();

    $counter = 1;

    foreach ($order as $table) {
        foreach ($files as $file) {
            if (str_contains($file, "create_{$table}_table")) {
                $newName = sprintf(
                    '2026_03_15_%06d_%s',
                    $counter++,
                    substr($file, 18)
                );

                rename(
                    "$basePath/$file",
                    "$basePath/$newName"
                );

                $this->info("$file ? $newName");
            }
        }
    }

    $this->info('Migration files reordered.');
}

}
