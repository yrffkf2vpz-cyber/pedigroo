<?php

namespace App\Services\Agent;

use Illuminate\Contracts\Container\BindingResolutionException;

class PedrooAgentValidatorService
{
    /**
     * Fájl létezésének ellenőrzése a projekt gyökeréhez képest.
     */
    public function validateFileExists(string $relativePath): array
    {
        $fullPath = base_path($relativePath);

        $exists = file_exists($fullPath);

        return [
            'check'   => 'file_exists',
            'path'    => $relativePath,
            'full'    => $fullPath,
            'exists'  => $exists,
        ];
    }

    /**
     * Class létezésének ellenőrzése (autoload + class_exists).
     */
    public function validateClassExists(string $fqcn): array
    {
        $exists = class_exists($fqcn);

        return [
            'check'   => 'class_exists',
            'class'   => $fqcn,
            'exists'  => $exists,
        ];
    }

    /**
     * Container resolution ellenőrzése (app()->make()).
     */
    public function validateContainerResolution(string $fqcn): array
    {
        try {
            app()->make($fqcn);

            return [
                'check'    => 'container_resolution',
                'class'    => $fqcn,
                'resolves' => true,
                'error'    => null,
            ];
        } catch (BindingResolutionException $e) {
            return [
                'check'    => 'container_resolution',
                'class'    => $fqcn,
                'resolves' => false,
                'error'    => $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            return [
                'check'    => 'container_resolution',
                'class'    => $fqcn,
                'resolves' => false,
                'error'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Komplett validáció egy service-re / class-ra.
     */
    public function validateService(string $relativePath, string $fqcn): array
    {
        $fileCheck  = $this->validateFileExists($relativePath);
        $classCheck = $this->validateClassExists($fqcn);
        $container  = $this->validateContainerResolution($fqcn);

        return [
            'file'      => $fileCheck,
            'class'     => $classCheck,
            'container' => $container,
        ];
    }
    public function validateExistingFiles(): array
{
    $roots = [
        'app/Services',
        'app/Http/Controllers',
        'app/Models',
    ];

    $results = [];

    foreach ($roots as $root) {
        $dir = base_path($root);

        if (!is_dir($dir)) {
            $results[] = [
                'root'   => $root,
                'exists' => false,
                'error'  => 'Directory not found',
            ];
            continue;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $fullPath     = $file->getPathname();
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $fullPath);

            // Namespace + class név becslése PSR-4 szerint
            $class = $this->guessClassFromPath($relativePath);

            $fileCheck  = $this->validateFileExists($relativePath);
            $classCheck = $this->validateClassExists($class);
            $container  = $this->validateContainerResolution($class);

            $results[] = [
                'path'      => $relativePath,
                'class'     => $class,
                'file'      => $fileCheck,
                'class_ok'  => $classCheck,
                'container' => $container,
            ];
        }
    }

    return $results;
}

/**
 * Egyszerű PSR-4 alapú becslés: app/Services/Foo/Bar.php
 * → App\Services\Foo\Bar
 */
private function guessClassFromPath(string $relativePath): string
{
    $relativePath = str_replace(['/', '\\'], '\\', $relativePath);

    if (str_starts_with($relativePath, 'app\\')) {
        $relativePath = substr($relativePath, 4); // "app\" levágása
    }

    $relativePath = preg_replace('/\.php$/', '', $relativePath);

    return 'App\\' . $relativePath;
}
}