<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PedrooScanPipeline extends Command
{
    protected $signature = 'pedroo:scan-pipeline';
    protected $description = 'Scan Normalize pipeline steps and store them in pd_pedroo_registry';

    public function handle(): int
    {
        $this->info('Scanning Normalize pipeline...');

        $this->scanNormalizePipeline();

        $this->info('Pipeline scan completed and stored in registry.');
        return self::SUCCESS;
    }

    private function scanNormalizePipeline(): void
    {
        $path = base_path('app/Services/Normalizers');

        if (!is_dir($path)) {
            $this->warn("Normalize directory not found: {$path}");
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $fullPath = $file->getPathname();
            $relativePath = str_replace(base_path() . '/', '', $fullPath);

            $content = file_get_contents($fullPath);
            $isEmpty = trim($content) === '';
            $isSkeleton = $this->detectSkeleton($content);

            $order = $this->calculateOrderScore($relativePath);

            DB::table('pd_pedroo_registry')->updateOrInsert(
                [
                    'entity_type' => 'pipeline_step',
                    'entity_name' => $relativePath,
                ],
                [
                    'module' => 'normalize',
                    'status' => $isEmpty ? 'empty' : ($isSkeleton ? 'skeleton' : 'ok'),
                    'details' => json_encode([
                        'path' => $relativePath,
                        'class' => $this->extractClassName($content),
                        'namespace' => $this->extractNamespace($content),
                        'methods' => $this->extractMethods($content),
                        'order' => $order,
                    ]),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $this->line(" - Normalize step: {$relativePath}");
        }
    }

    private function detectSkeleton(string $content): bool
    {
        return str_contains($content, 'TODO')
            || str_contains($content, 'return [];')
            || preg_match('/class\s+\w+\s*{\s*}/', $content);
    }

    private function extractClassName(string $content): ?string
    {
        if (preg_match('/class\s+(\w+)/', $content, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractNamespace(string $content): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', $content, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function extractMethods(string $content): array
    {
        preg_match_all('/function\s+(\w+)\s*\(/', $content, $m);
        return $m[1] ?? [];
    }

    private function calculateOrderScore(string $path): int
    {
        $score = 0;
        $lower = strtolower($path);

        if (str_contains($lower, 'detector')) $score += 100;
        if (str_contains($lower, 'parser')) $score += 200;
        if (str_contains($lower, 'rules')) $score += 300;
        if (str_contains($lower, 'normalizer')) $score += 400;
        if (str_contains($lower, 'promotion')) $score += 500;
        if (str_contains($lower, 'service')) $score += 600;
        if (str_contains($lower, 'writer')) $score += 700;

        return $score;
    }
}