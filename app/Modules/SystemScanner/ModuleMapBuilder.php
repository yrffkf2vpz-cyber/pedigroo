<?php

namespace App\Modules\SystemScanner;

use Illuminate\Support\Facades\File;

class ModuleMapBuilder
{
    protected array $modules = [];

    public function build(array $fileMap, array $usageMap): array
    {
        $this->detectModules($fileMap);
        $this->assignFilesToModules($fileMap);
        $this->calculateModuleUsage($usageMap);
        $this->detectDependencies($fileMap);

        return $this->modules;
    }

    /**
     * 1) Modulok felismerése a könyvtárstruktúrából.
     */
    protected function detectModules(array $fileMap): void
    {
        foreach ($fileMap as $path => $info) {
            if (str_contains($path, 'app/Modules/')) {
                $module = $this->extractModuleName($path);
                $this->modules[$module] ??= [
                    'name' => $module,
                    'files' => [],
                    'usage' => 0,
                    'dependencies' => [],
                    'status' => 'unknown',
                ];
            }
        }
    }

    /**
     * 2) Fájlok hozzárendelése modulokhoz.
     */
    protected function assignFilesToModules(array $fileMap): void
    {
        foreach ($fileMap as $path => $info) {
            $module = $this->extractModuleName($path);

            if ($module) {
                $this->modules[$module]['files'][] = [
                    'path' => $path,
                    'class' => $info['class'],
                    'uses' => $info['uses'],
                ];
            }
        }
    }

    /**
     * 3) Modul aktivitás számítása.
     */
    protected function calculateModuleUsage(array $usageMap): void
    {
        foreach ($this->modules as $module => &$data) {
            $hits = 0;

            foreach ($data['files'] as $file) {
                $hits += $usageMap[$file['path']] ?? 0;
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
     * 4) Modulok közötti függoségek feltérképezése.
     */
    protected function detectDependencies(array $fileMap): void
    {
        foreach ($this->modules as $module => &$data) {
            $dependencies = [];

            foreach ($data['files'] as $file) {
                foreach ($file['uses'] as $use) {
                    $depModule = $this->extractModuleFromNamespace($use);

                    if ($depModule && $depModule !== $module) {
                        $dependencies[$depModule] = true;
                    }
                }
            }

            $data['dependencies'] = array_keys($dependencies);
        }
    }

    // -----------------------------------------------
    // Segédfüggvények
    // -----------------------------------------------

    protected function extractModuleName(string $path): ?string
    {
        if (!str_contains($path, 'app/Modules/')) {
            return null;
        }

        $after = explode('app/Modules/', $path)[1];
        return explode('/', $after)[0];
    }

    protected function extractModuleFromNamespace(string $namespace): ?string
    {
        if (!str_contains($namespace, 'App\\Modules\\')) {
            return null;
        }

        $after = explode('App\\Modules\\', $namespace)[1];
        return explode('\\', $after)[0];
    }
}
