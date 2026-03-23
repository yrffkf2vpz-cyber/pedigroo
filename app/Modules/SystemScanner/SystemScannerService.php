<?php

namespace App\Modules\SystemScanner;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemScannerService
{
    protected array $fileMap = [];
    protected array $classMap = [];
    protected array $usageMap = [];

    /**
     * Statikus fájlrendszer beolvasás.
     */
    public function scanFilesystem(string $basePath = null): array
    {
        $basePath = $basePath ?? base_path();

        $files = File::allFiles($basePath);

        foreach ($files as $file) {
            $path = $file->getRealPath();

            if ($this->isPhpFile($path)) {
                $this->fileMap[$path] = [
                    'path' => $path,
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'class' => $this->extractClassName($path),
                    'uses' => $this->extractUses($path),
                ];
            }
        }

        return $this->fileMap;
    }

    /**
     * Runtime hívások logolása.
     */
    public function trackUsage(string $file): void
    {
        if (!isset($this->usageMap[$file])) {
            $this->usageMap[$file] = 0;
        }

        $this->usageMap[$file]++;
    }

    /**
     * Fájlok összevetése a runtime használattal.
     */
    public function analyzeUsage(): array
    {
        $report = [];

        foreach ($this->fileMap as $path => $info) {
            $report[$path] = [
                'class' => $info['class'],
                'uses' => $info['uses'],
                'runtime_hits' => $this->usageMap[$path] ?? 0,
                'status' => $this->usageMap[$path] > 0 ? 'active' : 'possibly_unused',
            ];
        }

        return $report;
    }

    /**
     * Függoségi gráf építése.
     */
    public function buildDependencyGraph(): array
    {
        $graph = [];

        foreach ($this->fileMap as $path => $info) {
            $graph[$path] = $info['uses'];
        }

        return $graph;
    }

    /**
     * Teljes rendszerjelentés.
     */
    public function generateReport(): array
    {
        return [
            'files' => $this->fileMap,
            'usage' => $this->usageMap,
            'analysis' => $this->analyzeUsage(),
            'dependencies' => $this->buildDependencyGraph(),
        ];
    }

    // -----------------------------------------------
    // Segédfüggvények
    // -----------------------------------------------

    protected function isPhpFile(string $path): bool
    {
        return str_ends_with($path, '.php');
    }

    protected function extractClassName(string $path): ?string
    {
        $content = File::get($path);

        if (preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function extractUses(string $path): array
    {
        $content = File::get($path);

        preg_match_all('/use\s+([^;]+);/', $content, $matches);

        return $matches[1] ?? [];
    }
}
