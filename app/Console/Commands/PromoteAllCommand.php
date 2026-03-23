<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Promotion\PromotionRunner;

class PromoteAllCommand extends Command
{
    protected $signature = 'promote:all';
    protected $description = 'Promote all pending Pedroo sandbox records';

    public function handle(PromotionRunner $runner)
    {
        $this->info("=== PROMOTION STARTED ===");

        $runner->promoteResults($this);
        $runner->promoteHealth($this);
        $runner->promoteBreeders($this);
        $runner->promoteOwners($this);
        $runner->promoteKennels($this);
        $runner->promoteParents($this);
        $runner->promoteEvents($this);
        $runner->promoteHistory($this);
        $runner->promoteTitles($this);
        $runner->promoteChampionships($this);

        $this->info("=== PROMOTION FINISHED ===");
    }
}