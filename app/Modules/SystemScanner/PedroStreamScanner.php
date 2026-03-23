<?php

namespace App\Modules\SystemScanner;

use Illuminate\Support\Facades\File;

class PedroStreamScanner
{
    public function streamFiles(callable $callback): void
    {
        $base = base_path();

        foreach (File::allFiles($base) as $file) {
            $path = $file->getRealPath();

            if (!str_ends_with($path, '.php')) {
                continue;
            }

            // Egyszerre csak 1 fájlt adunk át
            $callback($path);
        }
    }
}
