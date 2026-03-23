<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Pedroo\MasterPlanTaskParser;

class PedrooParseMasterPlanCommand extends Command
{
    protected $signature = 'pedroo:parse-master-plan';
    protected $description = 'Parse Master Plan and generate pipeline tasks';

    public function handle()
    {
        $this->info("\n=== PEDROO MASTER PLAN ? TASK PARSER ===\n");

        $parser = app(MasterPlanTaskParser::class);
        $result = $parser->parse();

        if (isset($result['error'])) {
            $this->error($result['error']);
            return Command::FAILURE;
        }

        $count = $result['created_count'];

        $this->info("? {$count} ?j pipeline task l?trehozva.");
        $this->info("? A pipeline_tasks t?bla felt?ltve.");
        $this->info("\nMost m?r ind?thatod a NAGY RUN-t a pedroo:pipeline paranccsal.\n");

        return Command::SUCCESS;
    }
}
