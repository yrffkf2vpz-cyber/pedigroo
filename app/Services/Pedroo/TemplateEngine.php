<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Str;
use App\Models\GeneratedFile;

class TemplateEngine
{
    public function generate(string $module, string $task, array $payload): string
    {
        $blueprint = ModuleBlueprints::get($module);

        $results = [];

        foreach ($blueprint as $item) {

            $templateKey = $item['template'];
            $suffix = $item['suffix'];

            $templatePath = resource_path("pedroo/templates/{$templateKey}.stub");

            if (!file_exists($templatePath)) {
                $results[] = "Template not found: {$templateKey}";
                continue;
            }

            $template = file_get_contents($templatePath);

            $className = Str::studly($task) . $suffix;
            $namespace = "App\\Pedroo\\" . Str::studly($module);

            $output = str_replace(
                ['{{ namespace }}', '{{ class }}'],
                [$namespace, $className],
                $template
            );

            $dir = app_path("Pedroo/" . Str::studly($module));
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $filePath = "{$dir}/{$className}.php";
            file_put_contents($filePath, $output);

            // Hash + DB log
            $hash = md5($output);

            GeneratedFile::updateOrCreate(
                [
                    'module' => $module,
                    'task' => $task,
                    'file_path' => $filePath,
                ],
                [
                    'hash' => $hash,
                ]
            );

            $results[] = "Generated: {$filePath}";
        }

        return implode("\n", $results);
    }
}

