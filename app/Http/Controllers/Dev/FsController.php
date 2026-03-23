<?php

namespace App\Http\Controllers\Dev;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FsController
{
    public function list(Request $request)
    {
        $path = base_path($request->get('path'));

        if (!File::exists($path)) {
            return response()->json([
                'items' => [],
            ]);
        }

        return response()->json([
            'items' => $this->scan($path),
        ]);
    }

    private function scan($path)
    {
        $result = [];

        foreach (File::directories($path) as $dir) {
            $result[] = [
                'type' => 'dir',
                'name' => basename($dir),
                'path' => $dir,
                'children' => $this->scan($dir),
            ];
        }

        foreach (File::files($path) as $file) {
            $result[] = [
                'type' => 'file',
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
            ];
        }

        return $result;
    }
}