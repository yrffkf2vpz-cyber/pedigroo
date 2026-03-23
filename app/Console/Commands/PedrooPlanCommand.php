<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PedrooPlanCommand extends Command
{
    protected $signature = 'pedroo:plan {--module=} {--check}';
    protected $description = 'Pedroo Master Plan megjelen?t?se ?s ellenorz?se';

    public function handle()
    {
        $plan = config('pedroo_plan');
        $moduleFilter = $this->option('module');
        $runCheck = $this->option('check');

        $this->info("\n=== PEDROO MASTER PLAN ===\n");

        foreach ($plan as $moduleKey => $module) {

            if ($moduleFilter && $moduleFilter !== $moduleKey) {
                continue;
            }

            $this->line("\n" . strtoupper($module['title']));
            $this->line(str_repeat('-', strlen($module['title'])));

            foreach ($module['tasks'] as $taskKey => $done) {

                $status = $done ? '?' : ' ';

                // Ha a user k?rte a --check opci?t, akkor automatikusan ellenorz?nk
                if ($runCheck) {
                    $auto = $this->autoCheck($moduleKey, $taskKey);
                    if ($auto === true) {
                        $status = '?';
                    }
                }

                $this->line("[$status] $taskKey");
            }
        }

        $this->line("\nK?sz.");
        return Command::SUCCESS;
    }

    /**
     * Automatikus ellenorz?s egy taskhoz.
     * K?sobb bov?theto: f?jlok, classok, met?dusok, pipeline integr?ci?.
     */
    private function autoCheck(string $module, string $task)
    {
        $checks = [

            // P?lda: DiagnosisNormalizer l?tezik
            'normalize.diagnosis_normalization' => function () {
                return File::exists(app_path('Services/Diagnosis/DiagnosisNormalizer.php'));
            },

            // P?lda: NormalizePipelineService l?tezik
            'normalize.normalize_api_endpoint' => function () {
                return File::exists(app_path('Http/Controllers/NormalizeController.php'));
            },

            // P?lda: Ingest UI f?jlok
            'ingest.excel_import_ui' => function () {
                return File::exists(resource_path('views/ingest/excel.blade.php'));
            },

            // stb... k?sobb bov?theto
        ];

        $key = $module . '.' . $task;

        if (isset($checks[$key])) {
            return $checks[$key]();
        }

        return false;
    }
}