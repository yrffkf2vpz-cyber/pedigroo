<?php

namespace App\Modules\SystemScanner;

class CommandMapBuilder
{
    protected array $commands = [];

    public function build(array $fileMap, array $usageMap): array
    {
        $this->detectCommands($fileMap);
        $this->summarizeCommandUsage($usageMap);
        $this->summarizeDependencies($fileMap);
        $this->categorizeCommands();

        return $this->commands;
    }

    /**
     * 1) Console parancsok felismerése.
     */
    protected function detectCommands(array $fileMap): void
    {
        foreach ($fileMap as $path => $info) {
            if (str_contains($path, 'Console/Commands')) {
                $command = $this->extractCommandName($path);

                if ($command) {
                    $this->commands[$command] ??= [
                        'name' => $command,
                        'usage' => 0,
                        'dependency_count' => 0,
                        'status' => 'unknown',
                        'category' => 'misc',
                    ];
                }
            }
        }
    }

    /**
     * 2) Command aktivitás összegzése.
     */
    protected function summarizeCommandUsage(array $usageMap): void
    {
        foreach ($this->commands as $command => &$data) {
            $hits = 0;

            foreach ($usageMap as $file => $count) {
                if (str_contains($file, $command)) {
                    $hits += $count;
                }
            }

            $data['usage'] = $hits;

            if ($hits > 100) {
                $data['status'] = 'active';
            } elseif ($hits > 0) {
                $data['status'] = 'low_activity';
            } else {
                $data['status'] = 'possibly_unused';
            }
        }
    }

    /**
     * 3) Függoségek összegzése (nem listázás!).
     */
    protected function summarizeDependencies(array $fileMap): void
    {
        foreach ($this->commands as $command => &$data) {
            $count = 0;

            foreach ($fileMap as $path => $info) {
                if (!str_contains($path, $command)) {
                    continue;
                }

                foreach ($info['uses'] as $use) {
                    if (str_contains($use, 'Service') ||
                        str_contains($use, 'Pipeline') ||
                        str_contains($use, 'Repository')) {
                        $count++;
                    }
                }
            }

            $data['dependency_count'] = $count;
        }
    }

    /**
     * 4) Command kategorizálása.
     */
    protected function categorizeCommands(): void
    {
        foreach ($this->commands as $command => &$data) {
            $name = strtolower($command);

            $data['category'] =
                str_contains($name, 'ingest') ? 'ingest' :
                (str_contains($name, 'normalize') ? 'normalize' :
                (str_contains($name, 'promote') ? 'promote' :
                (str_contains($name, 'scan') ? 'maintenance' :
                (str_contains($name, 'export') ? 'export' : 'misc'))));
        }
    }

    // -----------------------------------------------
    // Segédfüggvény
    // -----------------------------------------------

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
}
