<?php

namespace App\Services\FileManager;

use Illuminate\Support\Facades\File;

class FileManagerService
{
    public function createFolder(string $path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return ['status' => 'ok', 'message' => 'Folder created'];
    }

    public function createFile(string $path)
{
    $fullPath = base_path($path);
    $directory = dirname($fullPath);

    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0755, true);
    }

    if (!File::exists($fullPath)) {
        File::put($fullPath, '');
    }

    return ['status' => 'ok', 'message' => 'File created'];
}

    public function writeFile(string $path, string $content)
    {
        File::put($path, $content);

        return ['status' => 'ok', 'message' => 'File written'];
    }

    public function readFile(string $path)
    {
        if (!File::exists($path)) {
            return ['status' => 'error', 'message' => 'File not found'];
        }

        return [
            'status' => 'ok',
            'content' => File::get($path),
        ];
    }

    public function deleteFile(string $path)
    {
        if (File::exists($path)) {
            File::delete($path);
        }

        return ['status' => 'ok', 'message' => 'File deleted'];
    }

    public function list(string $path)
    {
        if (!File::exists($path)) {
            return ['status' => 'error', 'message' => 'Path not found'];
        }

        return [
            'status' => 'ok',
            'files' => File::files($path),
            'directories' => File::directories($path),
        ];
    }
}