<?php

namespace App\Modules\SystemScanner;

class StreamCommandAggregator
{
    public array $commands = [];

    public function processFile(string $path, array $uses): void
    {
        if (!str_contains($path, 'Console/Commands')) {
            return;
        }

        $command = $this->extractCommandName($path);

        if (!$command) {
            return;
        }

        $this->commands[$command] ??= [
            'dependency_count' => 0,
            'category' => 'misc',
        ];

        foreach ($uses as $use) {
            if (
                str_contains($use, 'Service') ||
                str_contains($use, 'Pipeline') ||
                str_contains($use, 'Repository')
            ) {
                $this->commands[$command]['dependency_count']++;
            }
        }

        $this->commands[$command]['category'] = $this->detectCategory($command);
    }

    protected function extractCommandName(string $path): ?string
    {
        $parts = explode('/', $path);

        foreach ($parts as $part) {
            if (str_ends_with($part, 'Command.php')) {
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
            : (str_contains($n, 'scan') ? 'maintenance'
            : (str_contains($n, 'export') ? 'export'
            : 'misc'))));
    }
}
