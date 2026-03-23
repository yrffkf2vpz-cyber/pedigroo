<?php

namespace App\Services\Pipeline;

use Illuminate\Support\Facades\File;

class PipelineTaskGenerator
{
    protected string $taskBasePath = 'app/Tasks';

    public function generateMissingTasks(): array
    {
        $pipelineTasks = config('pipeline.tasks', []);
        $generated = [];
        $existing  = [];
        $failed    = [];

        foreach ($pipelineTasks as $taskName) {
            $className = $this->taskNameToClass($taskName);
            $namespace = 'App\\Tasks';
            $dir       = base_path($this->taskBasePath);
            $filePath  = $dir . '/' . $className . '.php';

            try {
                if (File::exists($filePath)) {
                    $existing[] = $taskName;
                    continue;
                }

                File::ensureDirectoryExists($dir);
                File::put($filePath, $this->generateStub($namespace, $className, $taskName));
                $generated[] = $taskName;
            } catch (\Throwable $e) {
                $failed[] = [
                    'task'   => $taskName,
                    'error'  => $e->getMessage(),
                ];
            }
        }

        return compact('generated', 'existing', 'failed');
    }

    protected function taskNameToClass(string $task): string
    {
        // pl. "dogs:duplicate-detection" → "DogsDuplicateDetection"
        $clean = str_replace([':', '-'], ' ', $task);
        $clean = ucwords($clean);
        return str_replace(' ', '', $clean);
    }

    protected function generateStub(string $namespace, string $className, string $taskName): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use App\Tasks\Task;

class {$className} extends Task
{
    /**
     * Auto-generated skeleton for task: "{$taskName}".
     * TODO: implement real logic.
     */
    public function handle()
    {
        // TODO: Implement task logic for "{$taskName}"
        return "Task {$taskName} executed (skeleton).";
    }
}

PHP;
    }
}