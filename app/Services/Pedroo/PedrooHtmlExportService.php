<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PedrooHtmlExportService
{
    protected array $config;
    protected string $outputRoot;
    protected string $baseUrl;

    public function __construct()
    {
        $this->config = config('pedroo_html_exporter');
        $this->outputRoot = public_path($this->config['html_output']['root']);
        $this->baseUrl = $this->config['publishing']['base_url'];
    }

    /**
     * Main entry point: process all categories and export HTML files.
     */
    public function run(): void
    {
        foreach ($this->config['categories'] as $category => $path) {
            $this->processCategory($category, base_path($path));
        }
    }

    /**
     * Process a single category (e.g. services, dto, pipelines).
     */
    protected function processCategory(string $category, string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = File::allFiles($directory);
        $counter = 1;

        foreach ($files as $file) {
            $this->exportFile($file, $category, $counter);
            $counter++;

            // Human-friendly pacing
            sleep($this->config['processing']['delay_seconds_between_exports']);
        }
    }

    /**
     * Export a single file into an HTML review page.
     */
    protected function exportFile(\SplFileInfo $file, string $category, int $counter): void
    {
        $id = sprintf('%s-%04d', $category, $counter);
        $outputDir = $this->outputRoot . '/' . $category;

        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0775, true);
        }

        $htmlPath = $outputDir . '/' . $counter . '.html';

        $content = File::get($file->getRealPath());
        $meta = $this->buildMetadata($file, $category, $id);

        $html = $this->renderHtml($meta, $content);

        File::put($htmlPath, $html);
    }

    /**
     * Build metadata block for the HTML.
     */
    protected function buildMetadata(\SplFileInfo $file, string $category, string $id): array
    {
        return [
            'id' => $id,
            'category' => $category,
            'path' => $file->getRealPath(),
            'size_kb' => round($file->getSize() / 1024, 2),
            'mtime' => date('Y-m-d H:i:s', $file->getMTime()),
            'hash' => md5_file($file->getRealPath()),
            'url' => $this->baseUrl . '/' . $category . '/' . basename($file->getRealPath()) . '.html'
        ];
    }

    /**
     * Render the final HTML page.
     */
    protected function renderHtml(array $meta, string $content): string
    {
        $escaped = htmlspecialchars($content);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{$meta['id']} – Pedroo Review</title>
    <style>
        body { font-family: monospace; background: #111; color: #eee; padding: 20px; }
        .file-block { border: 1px solid #444; padding: 20px; }
        .file-header { font-size: 22px; margin-bottom: 10px; }
        .meta { color: #aaa; margin-bottom: 20px; }
        .content { background: #000; padding: 20px; white-space: pre-wrap; }
    </style>
</head>
<body>

<div class="file-block" id="{$meta['id']}">
    <div class="file-header">{$meta['id']}</div>

    <div class="meta">
        Path: {$meta['path']}<br>
        Category: {$meta['category']}<br>
        Size: {$meta['size_kb']} KB<br>
        Modified: {$meta['mtime']}<br>
        Hash: {$meta['hash']}<br>
    </div>

    <div class="content">{$escaped}</div>
</div>

</body>
</html>
HTML;
    }
}