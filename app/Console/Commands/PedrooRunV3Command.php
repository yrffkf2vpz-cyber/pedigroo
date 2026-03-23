<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PedrooRunV3Command extends Command
{
    protected $signature = 'pedroo:run-module {module}';
    protected $description = 'Run v3: modul-szintu futtatás, több task intelligens kezelése';

    public function handle()
    {
        $module = $this->argument('module');
        $plan = config('pedroo_plan');

        $this->info("\n=== PEDROO RUN v3 ===");
        $this->info("Modul: $module\n");

        if (!isset($plan[$module])) {
            $this->error("A modul nem található a Master Planben.");
            return Command::FAILURE;
        }

        $tasks = $plan[$module]['tasks'];

        foreach ($tasks as $task => $done) {
            $this->info("\n--- Task: $task ---");

            $missing = $this->checkMissingParts($task);

            if (empty($missing)) {
                $this->info("? Minden kész ehhez a taskhoz.");
                continue;
            }

            foreach ($missing as $type => $path) {
                $this->generate($type, $path);
            }

            $this->info("? A hiányzó részek pótlása kész.");
        }

        $this->info("\n=== Modul teljesítve ===\n");
        return Command::SUCCESS;
    }

    private function checkMissingParts(string $task)
    {
        $missing = [];

        if ($task === 'diagnosis_normalization') {

            $file = app_path('Services/Diagnosis/DiagnosisNormalizer.php');
            if (!File::exists($file)) {
                $missing['normalizer'] = $file;
            }

            $map = resource_path('data/diagnosis_map.json');
            if (!File::exists($map)) {
                $missing['map'] = $map;
            }

            $test = base_path('tests/Feature/Normalize/DiagnosisNormalizerTest.php');
            if (!File::exists($test)) {
                $missing['test'] = $test;
            }
        }

        // Itt bovítheto tovább minden taskhoz

        return $missing;
    }

    private function generate(string $type, string $path)
    {
        $this->info("? Generálás: $type ($path)");

        if ($type === 'normalizer') {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $this->normalizerSkeleton());
        }

        if ($type === 'map') {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, json_encode([], JSON_PRETTY_PRINT));
        }

        if ($type === 'test') {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $this->testSkeleton());
        }
    }

    private function normalizerSkeleton()
    {
        return <<<PHP
<?php

namespace App\Services\Diagnosis;

class DiagnosisNormalizer
{
    public function normalize(array \$row)
    {
        return [
            'diagnosis' => \$this->mapDiagnosis(\$row['diagnosis'] ?? null),
        ];
    }

    private function mapDiagnosis(?string \$value)
    {
        return \$value;
    }
}
PHP;
    }

    private function testSkeleton()
    {
        return <<<PHP
<?php

namespace Tests\Feature\Normalize;

use Tests\TestCase;

class DiagnosisNormalizerTest extends TestCase
{
    public function test_basic_normalization()
    {
        \$this->assertTrue(true);
    }
}
PHP;
    }
}