<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\File;

class PedrooEngine
{
    protected string $sourcePath;

    public function __construct()
    {
        $this->sourcePath = storage_path('old_laravel');
    }

    public function scan(): array
    {
        if (!File::exists($this->sourcePath)) {
            return [
                'status'  => 'error',
                'message' => 'A storage/old_laravel mappa nem található.',
                'files'   => [],
            ];
        }

        $files = File::allFiles($this->sourcePath);

        $mapped = collect($files)->map(function ($file) {
            return [
                'path'      => str_replace($this->sourcePath . '/', '', $file->getPathname()),
                'extension' => $file->getExtension(),
                'size'      => $file->getSize(),
            ];
        })->values()->all();

        return [
            'status'  => 'ok',
            'message' => 'Régi projekt beolvasva.',
            'summary' => [
                'total' => count($mapped),
                'php'   => count(array_filter($mapped, fn($f) => $f['extension'] === 'php')),
                'blade' => count(array_filter($mapped, fn($f) => str_contains($f['path'], '.blade.php'))),
            ],
            'files' => $mapped,
        ];
    }
}