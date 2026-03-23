<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FullHtmlExportCommand extends Command
{
    protected $signature = 'pedroo:export-full-html';
    protected $description = 'Exports the ENTIRE project as HTML with step markers for AI review.';

    private array $allFiles = [];
    private array $allDirs = [];

    public function handle()
    {
        $root = base_path();
        $outputDir = storage_path('pedroo/full-export');

        File::deleteDirectory($outputDir);
        File::makeDirectory($outputDir, 0755, true);

        // Collect all directories and files
        $this->allDirs = $this->scanDirectories($root);
        $this->allFiles = $this->scanFiles($root);

        // Export each file as HTML with step markers
        foreach ($this->allFiles as $index => $filePath) {
            $this->exportFileWithMarkers($filePath, $index, $outputDir);
        }

        $this->info("Full HTML export completed with step markers.");
    }

    private function scanDirectories($root)
    {
        return collect(File::directories($root))
            ->flatMap(function ($dir) {
                return array_merge([$dir], $this->scanDirectories($dir));
            })
            ->toArray();
    }

    private function scanFiles($root)
    {
        return collect(File::allFiles($root))
            ->map(fn($file) => $file->getPathname())
            ->toArray();
    }

    private function exportFileWithMarkers($filePath, $index, $outputDir)
    {
        $relative = str_replace(base_path(), '', $filePath);
        $htmlPath = $outputDir . $relative . '.html';

        File::ensureDirectoryExists(dirname($htmlPath));

        $content = File::get($filePath);
        $escaped = htmlspecialchars($content);

        // Determine next file
        $nextFile = $this->allFiles[$index + 1] ?? null;

        // Determine next directory
        $currentDir = dirname($filePath);
        $nextDir = $this->findNextDirectory($currentDir);

        // Build step markers
        $markers = "";

        if ($nextFile) {
            $markers .= "<!-- PEDROO_NEXT_FILE: " . str_replace(base_path(), '', $nextFile) . " -->\n";
        } else {
            $markers .= "<!-- PEDROO_NO_MORE_FILES -->\n";
        }

        if ($nextDir) {
            $markers .= "<!-- PEDROO_NEXT_DIR: " . str_replace(base_path(), '', $nextDir) . " -->\n";
        } else {
            $markers .= "<!-- PEDROO_END -->\n";
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{$relative}</title>
<style>
body { font-family: monospace; white-space: pre; }
</style>
</head>
<body>
{$escaped}

<hr>
{$markers}
</body>
</html>
HTML;

        File::put($htmlPath, $html);
    }

    private function findNextDirectory($currentDir)
    {
        $index = array_search($currentDir, $this->allDirs);

        if ($index === false) {
            return null;
        }

        return $this->allDirs[$index + 1] ?? null;
    }
}