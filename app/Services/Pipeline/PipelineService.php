<?php

namespace App\Services\Pipeline;

use App\Models\PipelineTask;
use App\Services\Pedroo\PedrooEngine;
use App\Services\Agent\PedrooAgentService;
use App\Services\Agent\PedrooAgentValidatorService;
use Illuminate\Support\Facades\Http;

class PipelineService
{
    /**
     * Új pipeline task létrehozása
     */
    public function addTask(string $type, ?string $payload = null): PipelineTask
    {
        return PipelineTask::create([
            'type'    => $type,
            'payload' => $payload,
            'status'  => 'pending',
            'log'     => null,
        ]);
    }

    /**
     * Egy konkrét task lefuttatása (KÖZPONTI VÉGREHAJTÓ MOTOR)
     */
    public function runTask(PipelineTask $task): PipelineTask
    {
        // 1) Task státusz: running
        $task->update([
            'status' => 'running',
            'log'    => 'Futtatás indult...',
        ]);

        try {
            // ������ 1) Először megpróbáljuk AGENT-ként futtatni (lokális műveletek)
            $agentResult = $this->tryRunAgentTask($task);

            if ($agentResult !== null) {
                $task->update([
                    'status' => 'done',
                    'log'    => "Agent result: " . json_encode($agentResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                ]);

                return $task;
            }

            // ������ 2) Ha nem agent task, akkor AI-vezérelt pipeline
            $response = Http::timeout(60)->post(
                url('/api/ai/task'),
                [
                    'task'    => $task->type,
                    'payload' => $task->payload, // stringként küldjük, AI oldalon parse-oljuk
                ]
            );

            // AI válasz feldolgozása
            $aiJson = $response->json();

            $aiOutput = $aiJson['result']
                ?? $aiJson
                ?? $response->body()
                ?? 'AI response: null';

            // Task frissítése
            $task->update([
                'status' => 'done',
                'log'    => "AI response: " . json_encode($aiOutput, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);

        } catch (\Throwable $e) {

            // 3) Hiba esetén
            $task->update([
                'status' => 'error',
                'log'    => $e->getMessage(),
            ]);
        }

        return $task;
    }

    /**
     * Következő pending task lekérése
     */
    public function getNextTask(): ?PipelineTask
    {
        return PipelineTask::where('status', 'pending')
            ->orderBy('id')
            ->first();
    }

    /**
     * Következő pending task lefuttatása
     * → CSAK ROUTER, a runTask() a motor
     */
    public function runNextTask(): ?PipelineTask
    {
        $task = $this->getNextTask();

        if (!$task) {
            return null;
        }

        return $this->runTask($task);
    }

    /**
     * ������ AGENT TASKOK + VALIDÁCIÓ
     * Ha nem null-t ad vissza, akkor a taskot lokálisan kezeltük,
     * és NEM kell AI-hoz fordulni.
     */
    private function tryRunAgentTask(PipelineTask $task): ?array
    {
        $agent     = app(PedrooAgentService::class);
        $validator = app(PedrooAgentValidatorService::class);

        $payload = $task->payload ? json_decode($task->payload) : null;

        // 1) Fájl létrehozása
        if ($task->type === 'agent.file.create') {
            $ok = $agent->createFile($payload->path, $payload->contents);

            return [
                'action'  => 'file.create',
                'success' => $ok,
                'path'    => $payload->path,
            ];
        }

        // 2) Fájl frissítése
        if ($task->type === 'agent.file.update') {
            $ok = $agent->updateFile($payload->path, $payload->contents);

            return [
                'action'  => 'file.update',
                'success' => $ok,
                'path'    => $payload->path,
            ];
        }

        // 3) Fájl végére írás
        if ($task->type === 'agent.file.append') {
            $ok = $agent->appendToFile($payload->path, $payload->snippet);

            return [
                'action'  => 'file.append',
                'success' => $ok,
                'path'    => $payload->path,
            ];
        }

        // 4) SERVICE VALIDÁCIÓ
        if ($task->type === 'agent.validate.service') {
            $result = $validator->validateService($payload->path, $payload->class);

            return [
                'action' => 'validate.service',
                'path'   => $payload->path,
                'class'  => $payload->class,
                'result' => $result,
            ];
        }

        // 5) MEGLÉVŐ FÁJLOK TELJES VALIDÁCIÓJA
        if ($task->type === 'agent.validate.existing_files') {
            $result = $validator->validateExistingFiles();

            return [
                'action' => 'validate.existing_files',
                'result' => $result,
            ];
        }

        // Ha nem agent task → menjen AI-hoz
        return null;
    }

    /**
     * Régi placeholder – most már csak dokumentációs célra
     */
    private function execute(PipelineTask $task): string
    {
        return "AI vezérelt pipeline – nincs lokális végrehajtás.";
    }

    private function runPedrooEngine(): string
    {
        $engine = new PedrooEngine();
        $result = $engine->scan();

        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function syncDatabase(): string
    {
        return 'Adatbázis szinkron / előkészítés lefutott.';
    }

    private function generateModule(string $module): string
    {
        return "Modul generálva: {$module}";
    }
}