<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PedrooUtf8FixCommand extends Command
{
    protected $signature = 'pedroo:utf8-fix';
    protected $description = 'Konvertálja a projekt összes szöveges fájlját UTF-8-ra';

    public function handle()
    {
        $this->info("\n=== PEDROO UTF-8 KONVERZIÓ ===\n");

        $root = base_path();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root)
        );

        foreach ($iterator as $file) {

            if (!$file->isFile()) continue;

            $path = $file->getPathname();

            // Bináris fájlok kihagyása
            if (preg_match('/\.(jpg|jpeg|png|gif|pdf|zip|exe|bin|ico)$/i', $path)) {
                continue;
            }

            $content = file_get_contents($path);

            if ($content === false) continue;

            // Ha már UTF-8, nem kell konvertálni
            if (mb_detect_encoding($content, 'UTF-8', true)) {
                continue;
            }

            // Konvertálás UTF-8-ra
            $converted = mb_convert_encoding($content, 'UTF-8');

            file_put_contents($path, $converted);

            $this->info("Konvertálva: {$path}");
        }

        $this->info("\nKész. Minden fájl UTF-8-ra konvertálva.\n");

        return Command::SUCCESS;
    }
}
