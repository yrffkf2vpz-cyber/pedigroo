<?php

namespace App\Modules\SystemScanner;

class StreamModuleAggregator
{
    public array $modules = [];

    public function processFile(string $path, array $uses): void
    {
        if (!str_contains($path, 'app/Modules/')) {
            return;
        }

        $module = explode('app/Modules/', $path)[1];
        $module = explode('/', $module)[0];

        $this->modules[$module] ??= [
            'file_count' => 0,
            'dependency_count' => 0,
        ];

        $this->modules[$module]['file_count']++;

        foreach ($uses as $use) {
            if (str_contains($use, 'App\\Modules\\')) {
                $dep = explode('App\\Modules\\', $use)[1];
                $dep = explode('\\', $dep)[0];

                if ($dep !== $module) {
                    $this->modules[$module]['dependency_count']++;
                }
            }
        }
    }
}
