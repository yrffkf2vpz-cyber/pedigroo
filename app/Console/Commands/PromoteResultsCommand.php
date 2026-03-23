<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Promotion\PromotionRunner;

class PromoteResultsCommand extends Command
{
    protected $signature = 'pedroo:promote-results';
    protected $description = 'Promote all sandbox results into final pd_event_results';

    protected PromotionRunner $runner;

    public function __construct(PromotionRunner $runner)
    {
        parent::__construct();
        $this->runner = $runner;
    }

    public function handle(): int
    {
        $this->info("Starting promotion of pedroo_results...");

        $this->runner->promoteAll($this);

        $this->info("Promotion completed.");
        return Command::SUCCESS;
    }
}