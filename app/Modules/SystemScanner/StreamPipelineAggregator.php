<?php

namespace App\Modules\SystemScanner;

class StreamPipelineAggregator
{
    public array $pipelines = [];

    public function processFile(string $path, array $uses): void
    {
        if (!str_contains($path, 'Pipeline')) {
            return;
        }

        $pipeline = $this->extractPipelineName($path);

        if (!$pipeline) {
            return;
        }

        $this->pipelines[$pipeline] ??= [
            'task_count' => 0,
            'dependency_count' => 0,
            'category' => 'misc',
        ];

        $this->pipelines[$pipeline]['task_count']++;

        foreach ($uses as $use) {
            if (str_contains($use, 'Pipeline') && !str_contains($use, $pipeline)) {
                $this->pipelines[$pipeline]['dependency_count']++;
            }
        }

        $this->pipelines[$pipeline]['category'] = $this->detectCategory($pipeline);
    }

    protected function extractPipelineName(string $path): ?string
    {
        if (!str_contains($path, 'Pipeline')) {
            return null;
        }

        $parts = explode('/', $path);

        foreach ($parts as $part) {
            if (str_contains($part, 'Pipeline')) {
                return str_replace('.php', '', $part);
            }
        }

        return null;
    }

    protected function detectCategory(string $name): string
    {
        $n = strtolower($name);

        return str_contains($n, 'ingest') ? 'ingest'
            : (str_contains($n, 'normalize') ? 'normalize'
            : (str_contains($n, 'promote') ? 'promote'
            : (str_contains($n, 'stat') ? 'stats'
            : 'misc')));
    }
}
