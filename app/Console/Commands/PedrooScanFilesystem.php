<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class PedrooScanFilesystem extends Command
{
    protected $signature = 'pedroo:scan-filesystem';
    protected $description = 'Scan the entire project filesystem and store files in pd_pedroo_registry';

    public function handle(): int
    {
        $this->info('Scanning project filesystem...');

        $roots = [
            base_path('app'),
            base_path('config'),
            base_path('routes'),
            base_path('database/migrations'),
            base_path('resources/views'),
            base_path('resources/lang'),
            base_path('public'),
        ];

        foreach ($roots as $root) {
            if (!is_dir($root)) {
                continue;
            }

            $this->line("Root: {$root}");

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $fullPath = $file->getPathname();
                $relativePath = str_replace(base_path() . '/', '', $fullPath);

                if ($this->shouldSkip($relativePath)) {
                    continue;
                }

                $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
                $size = $file->getSize();
                $mtime = $file->getMTime();

                DB::table('pd_pedroo_registry')->updateOrInsert(
                    [
                        'entity_type' => 'file',
                        'entity_name' => $relativePath,
                    ],
                    [
                        'module' => $this->detectModuleFromPath($relativePath),
                        'status' => 'ok',
                        'details' => json_encode([
                            'path' => $relativePath,
                            'extension' => $extension,
                            'size' => $size,
                            'modified_at' => date('Y-m-d H:i:s', $mtime),
                        ]),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $this->line(" - File: {$relativePath}");
            }
        }

        // Egyedi f?jlok
        foreach (['composer.json', 'package.json', 'artisan'] as $single) {
            $fullPath = base_path($single);
            if (!is_file($fullPath)) {
                continue;
            }

            $relativePath = $single;
            $size = filesize($fullPath);
            $mtime = filemtime($fullPath);
            $extension = pathinfo($relativePath, PATHINFO_EXTENSION);

            DB::table('pd_pedroo_registry')->updateOrInsert(
                [
                    'entity_type' => 'file',
                    'entity_name' => $relativePath,
                ],
                [
                    'module' => 'core',
                    'status' => 'ok',
                    'details' => json_encode([
                        'path' => $relativePath,
                        'extension' => $extension,
                        'size' => $size,
                        'modified_at' => date('Y-m-d H:i:s', $mtime),
                    ]),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $this->line(" - File: {$relativePath}");
        }

        $this->info('Filesystem scan completed.');
        return self::SUCCESS;
    }

    private function shouldSkip(string $path): bool
    {
        $lower = strtolower($path);

        $skip = [
            'vendor/',
            'storage/',
            'node_modules/',
            '.git/',
            '.idea/',
            '.vscode/',
            'bootstrap/cache/',
            '.env',
        ];

        foreach ($skip as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function detectModuleFromPath(string $path): string
    {
        $lower = strtolower($path);

        if (str_contains($lower, 'normalizer') || str_contains($lower, 'normalizers')) {
            return 'normalize';
        }

        if (str_contains($lower, 'ingest')) {
            return 'ingest';
        }

        if (str_contains($lower, 'audit')) {
            return 'audit';
        }

        if (str_contains($lower, 'dog') || str_contains($lower, 'dogs')) {
            return 'dogdb';
        }

        return 'core';
    }
}