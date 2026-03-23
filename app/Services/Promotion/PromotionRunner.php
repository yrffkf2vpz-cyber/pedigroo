<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class PromotionRunner
{
    protected ResultPromotionService $results;
    protected HealthPromotionService $health;

    // További promote modulok
    protected BreederPromotionService $breeders;
    protected OwnerPromotionService $owners;
    protected KennelPromotionService $kennels;
    protected ParentPromotionService $parents;
    protected EventPromotionService $events;
    protected HistoryPromotionService $history;
    protected TitlePromotionService $titles;
    protected ChampionshipPromotionService $championships;

    public function __construct(
        ResultPromotionService $results,
        HealthPromotionService $health,
        BreederPromotionService $breeders,
        OwnerPromotionService $owners,
        KennelPromotionService $kennels,
        ParentPromotionService $parents,
        EventPromotionService $events,
        HistoryPromotionService $history,
        TitlePromotionService $titles,
        ChampionshipPromotionService $championships
    ) {
        $this->results       = $results;
        $this->health        = $health;
        $this->breeders      = $breeders;
        $this->owners        = $owners;
        $this->kennels       = $kennels;
        $this->parents       = $parents;
        $this->events        = $events;
        $this->history       = $history;
        $this->titles        = $titles;
        $this->championships = $championships;
    }

    /**
     * RESULTS
     */
    public function promoteResults(?object $console = null): void
    {
        DB::table('pedroo_results')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->results->promote($sandbox);
                        if ($console) $console->info("Result #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_results')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'checked_at' => now(),
                        ]);
                        if ($console) $console->error("Error result #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * HEALTH
     */
    public function promoteHealth(?object $console = null): void
    {
        DB::table('pedroo_health_records')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->health->promote($sandbox);
                        if ($console) $console->info("Health #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_health_records')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error health #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * BREEDERS
     */
    public function promoteBreeders(?object $console = null): void
    {
        DB::table('pedroo_breeders')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->breeders->promote($sandbox);
                        if ($console) $console->info("Breeder #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_breeders')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error breeder #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * OWNERS
     */
    public function promoteOwners(?object $console = null): void
    {
        DB::table('pedroo_owners')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->owners->promote($sandbox);
                        if ($console) $console->info("Owner #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_owners')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error owner #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * KENNELS
     */
    public function promoteKennels(?object $console = null): void
    {
        DB::table('pedroo_kennels')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->kennels->promote($sandbox);
                        if ($console) $console->info("Kennel #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_kennels')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error kennel #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * PARENTS
     */
    public function promoteParents(?object $console = null): void
    {
        DB::table('pedroo_parents')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->parents->promote($sandbox);
                        if ($console) $console->info("Parent #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_parents')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error parent #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * EVENTS
     */
    public function promoteEvents(?object $console = null): void
    {
        DB::table('pedroo_events')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->events->promote($sandbox);
                        if ($console) $console->info("Event #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_events')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error event #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * HISTORY
     */
    public function promoteHistory(?object $console = null): void
    {
        DB::table('pedroo_history')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->history->promote($sandbox);
                        if ($console) $console->info("History #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_history')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error history #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * TITLES
     */
    public function promoteTitles(?object $console = null): void
    {
        DB::table('pedroo_titles')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->titles->promote($sandbox);
                        if ($console) $console->info("Title #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_titles')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error title #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }

    /**
     * CHAMPIONSHIPS
     */
    public function promoteChampionships(?object $console = null): void
    {
        DB::table('pedroo_championships')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($console) {

                foreach ($chunk as $sandbox) {
                    try {
                        $id = $this->championships->promote($sandbox);
                        if ($console) $console->info("Championship #{$sandbox->id} → {$id}");
                    } catch (\Throwable $e) {
                        DB::table('pedroo_championships')->where('id', $sandbox->id)->update([
                            'status' => 'error',
                            'notes'  => $e->getMessage(),
                            'updated_at' => now(),
                        ]);
                        if ($console) $console->error("Error championship #{$sandbox->id}: ".$e->getMessage());
                    }
                }
            });
    }
}