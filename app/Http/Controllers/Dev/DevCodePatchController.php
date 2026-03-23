<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DevCodePatchController extends Controller
{
    /**
     * Get file content for AI processing.
     *
     * GET /api/dev/code/get?path=app/Services/Dog/DogProfileService.php
     */
    public function get(Request $request)
    {
        $basePath = base_path();
        $relative = $request->get('path');

        if (!$relative) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        $fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $relative);

        if (!$fullPath || !str_starts_with($fullPath, $basePath) || !File::exists($fullPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->json([
            'path'    => $relative,
            'content' => File::get($fullPath),
        ]);
    }

    /**
     * Apply AI-generated patch to a file.
     *
     * POST /api/dev/code/patch
     * body: { "path": "...", "content": "..." }
     */
    public function patch(Request $request)
    {
        $basePath = base_path();
        $relative = $request->get('path');
        $content  = $request->get('content');

        if (!$relative || $content === null) {
            return response()->json(['error' => 'Path and content are required'], 400);
        }

        $fullPath = $basePath . DIRECTORY_SEPARATOR . $relative;
        $dir = dirname($fullPath);

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($fullPath, $content);

        return response()->json([
            'status' => 'patched',
            'path'   => $relative,
        ]);
    }
}