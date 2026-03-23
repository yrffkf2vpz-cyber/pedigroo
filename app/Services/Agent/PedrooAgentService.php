<?php

namespace App\Services\Agent;

class PedrooAgentService
{
    /**
     * A projekt gyökérkönyvtára.
     */
    protected string $basePath;

    /**
     * Engedélyezett (whitelist) gyökérútvonalak, amelyek alá írhat az agent.
     *
     * Pl.: app/Services, app/Http/Controllers, routes, database/migrations
     */
    protected array $allowedRoots = [
        'app/Services',
        'app/Http/Controllers',
        'routes',
        'database/migrations',
    ];

    public function __construct()
    {
        $this->basePath = base_path();
    }

    /**
     * Új fájl létrehozása (ha a könyvtár nem létezik, létrehozza).
     */
    public function createFile(string $relativePath, string $contents): bool
    {
        $fullPath = $this->sanitizePath($relativePath);

        $this->ensureDirectory(dirname($fullPath));

        return file_put_contents($fullPath, $contents) !== false;
    }

    /**
     * Meglévő fájl teljes felülírása.
     */
    public function updateFile(string $relativePath, string $contents): bool
    {
        $fullPath = $this->sanitizePath($relativePath);

        if (! $this->fileExists($relativePath)) {
            // opcionálisan: dönthetsz úgy, hogy ilyenkor false vagy create
            $this->ensureDirectory(dirname($fullPath));
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    /**
     * Szöveg hozzáfűzése egy fájl végéhez (pl. új route, új use, stb.).
     */
    public function appendToFile(string $relativePath, string $snippet): bool
    {
        $fullPath = $this->sanitizePath($relativePath);

        $this->ensureDirectory(dirname($fullPath));

        $handle = fopen($fullPath, file_exists($fullPath) ? 'a' : 'w');

        if (! $handle) {
            return false;
        }

        fwrite($handle, $snippet);
        fclose($handle);

        return true;
    }

    /**
     * Könyvtár létrehozása (rekurzívan), ha nem létezik.
     */
    public function ensureDirectory(string $directoryPath): void
    {
        if (! is_dir($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }
    }

    /**
     * Ellenőrzi, hogy létezik-e a fájl a whitelistelt területen.
     */
    public function fileExists(string $relativePath): bool
    {
        $fullPath = $this->sanitizePath($relativePath);

        return file_exists($fullPath);
    }

    /**
     * Relatív útvonalból teljes, biztonságos útvonalat készít,
     * és ellenőrzi, hogy az engedélyezett gyökerek valamelyike alá esik-e.
     */
    protected function sanitizePath(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/\\');

        // Ellenőrzés: csak az allowedRoots valamelyike alatt engedjük az írást
        $allowed = false;
        foreach ($this->allowedRoots as $root) {
            $normalizedRoot = trim($root, '/\\');
            if (str_starts_with($relativePath, $normalizedRoot)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            throw new \RuntimeException("PedrooAgentService: a megadott útvonal nem engedélyezett: {$relativePath}");
        }

        return $this->basePath . DIRECTORY_SEPARATOR . $relativePath;
    }
}