<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DevFileSystemController extends Controller
{
    /**
     * List files and directories under a given path.
     *
     * GET /api/dev/fs/list?path=app/Services
     */
    public function list(Request $request)
    {
        $basePath = base_path();
        $relative = $request->get('path', '');
        $path = realpath($basePath . DIRECTORY_SEPARATOR . $relative);

        if (!$path || !str_starts_with($path, $basePath)) {
            return response()->json(['error' => 'Invalid path'], 400);
        }

        $files = [];
        $dirs  = [];

        foreach (File::directories($path) as $dir) {
            $dirs[] = [
                'name' => basename($dir),
                'path' => str_replace($basePath . DIRECTORY_SEPARATOR, '', $dir),
                'type' => 'dir',
            ];
        }

        foreach (File::files($path) as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'path' => str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getRealPath()),
                'type' => 'file',
                'size' => $file->getSize(),
            ];
        }

        return response()->json([
            'path'  => $relative,
            'dirs'  => $dirs,
            'files' => $files,
        ]);
    }

    /**
     * Read a file content.
     *
     * GET /api/dev/fs/read?path=app/Services/Dog/DogProfileService.php
     */
    public function read(Request $request)
    {
        $basePath = base_path();
        $relative = $request->get('path');
        if (!$relative) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        $path = realpath($basePath . DIRECTORY_SEPARATOR . $relative);

        if (!$path || !str_starts_with($path, $basePath) || !File::exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if (File::isDirectory($path)) {
            return response()->json(['error' => 'Path is a directory'], 400);
        }

        return response()->json([
            'path'    => $relative,
            'content' => File::get($path),
        ]);
    }

    /**
     * Write (create or overwrite) a file.
     *
     * POST /api/dev/fs/write
     * body: { "path": "app/Services/Test.php", "content": "..." }
     */
    public function write(Request $request)
    {
        $basePath = base_path();
        $relative = $request->get('path');
        $content  = $request->get('content');

        if (!$relative) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        $fullPath = $basePath . DIRECTORY_SEPARATOR . $relative;
        $realDir  = dirname($fullPath);

        if (!str_starts_with(realpath($realDir) ?: $realDir, $basePath)) {
            return response()->json(['error' => 'Invalid path'], 400);
        }

        if (!File::isDirectory($realDir)) {
            File::makeDirectory($realDir, 0755, true);
        }

        File::put($fullPath, $content ?? '');

        return response()->json([
            'status' => 'written',
            'path'   => $relative,
        ]);
    }

    /**
     * Delete a file.
     *
     * DELETE /api/dev/fs/delete
     * body: { "path": "app/Services/Test.php" }
     */
    public function delete(Request $request)
    {
        $basePath = base_path();
        $relative = $request->get('path');

        if (!$relative) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        $fullPath = $basePath . DIRECTORY_SEPARATOR . $relative;
        $realPath = realpath($fullPath);

        if (!$realPath || !str_starts_with($realPath, $basePath) || !File::exists($realPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if (File::isDirectory($realPath)) {
            return response()->json(['error' => 'Refusing to delete directory'], 400);
        }

        File::delete($realPath);

        return response()->json([
            'status' => 'deleted',
            'path'   => $relative,
        ]);
    }
}