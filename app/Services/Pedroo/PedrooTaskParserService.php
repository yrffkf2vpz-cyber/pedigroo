<?php

namespace App\Services\Pedroo;

use App\Services\Pipeline\PipelineService;

class PedrooTaskParserService
{
    public function parseAndQueueTasks(): array
    {
        $path = resource_path('views/pedroo_plan.php');

        if (!file_exists($path)) {
            return ['error' => 'views/pedroo_plans.php not found'];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $pipeline = app(PipelineService::class);
        $createdTasks = [];

        foreach ($lines as $line) {

            $line = trim($line);

            // csak a "-" jellel kezdődő sorokat kezeljük
            if (!str_starts_with($line, '-')) {
                continue;
            }

            $instruction = ltrim($line, '- ');

            // 🔥 1) Service generálás
            if (str_starts_with($instruction, 'Create ') && str_contains($instruction, 'Service')) {

                $serviceName = str_replace('Create ', '', $instruction);
                $serviceName = trim($serviceName);

                $path = "app/Services/Promotion/{$serviceName}.php";
                $contents = app(PedrooCodeGenerator::class)->generateService($serviceName);

                $task = $pipeline->addTask('agent.file.create', json_encode([
                    'path'     => $path,
                    'contents' => $contents,
                ]));

                $createdTasks[] = $task;
                continue;
            }

            // 🔥 2) Route hozzáadása
            if (str_starts_with($instruction, 'Add route')) {

                $snippet = app(PedrooCodeGenerator::class)->generateRoute($instruction);

                $task = $pipeline->addTask('agent.file.append', json_encode([
                    'path'    => 'routes/web.php',
                    'snippet' => $snippet,
                ]));

                $createdTasks[] = $task;
                continue;
            }

            // 🔥 3) Controller generálás
            if (str_contains($instruction, 'controller')) {

                $controllerName = app(PedrooCodeGenerator::class)->extractControllerName($instruction);
                $contents = app(PedrooCodeGenerator::class)->generateController($controllerName);

                $task = $pipeline->addTask('agent.file.create', json_encode([
                    'path'     => "app/Http/Controllers/{$controllerName}.php",
                    'contents' => $contents,
                ]));

                $createdTasks[] = $task;
                continue;
            }

            // 🔥 4) Migration generálás
            if (str_contains($instruction, 'migration')) {

                $migration = app(PedrooCodeGenerator::class)->generateMigration($instruction);

                $task = $pipeline->addTask('agent.file.create', json_encode([
                    'path'     => "database/migrations/{$migration['filename']}",
                    'contents' => $migration['contents'],
                ]));

                $createdTasks[] = $task;
                continue;
            }
        }

        return $createdTasks;
    }
}