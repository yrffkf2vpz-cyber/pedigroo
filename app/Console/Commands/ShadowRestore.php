<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShadowRestore extends Command
{
    protected $signature = 'pedroo:shadow-restore {snapshot}';
    protected $description = 'Restore app directory from a _shadow_backup snapshot';

    public function handle(): int
    {
        $snapshotName = $this->argument('snapshot');

        $appPath    = base_path('app');
        $backupRoot = base_path('_shadow_backup');
        $snapshot   = $backupRoot . DIRECTORY_SEPARATOR . $snapshotName;

        if (! is_dir($snapshot)) {
            $this->error("Snapshot not found: {$snapshot}");
            return self::FAILURE;
        }

        $this->info("Restoring from snapshot: {$snapshot}");

        // Optional: backup current app before overwrite
        $preBackup = $backupRoot . DIRECTORY_SEPARATOR . 'pre_restore_' . now()->format('Y-m-d_H-i-s');
        if (is_dir($appPath)) {
            $this->info("Backing up current app to: {$preBackup}");
            $this->recursiveCopy($appPath, $preBackup);
        }

        // Remove current app directory
        $this->recursiveDelete($appPath);

        // Restore snapshot
        $this->recursiveCopy($snapshot, $appPath);

        $this->info('Shadow restore completed.');
        return self::SUCCESS;
    }

    protected function recursiveCopy(string $source, string $destination): void
    {
        $dir = opendir($source);

        @mkdir($destination, 0755, true);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $src = $source . DIRECTORY_SEPARATOR . $file;
            $dst = $destination . DIRECTORY_SEPARATOR . $file;

            if (is_dir($src)) {
                $this->recursiveCopy($src, $dst);
            } else {
                copy($src, $dst);
            }
        }

        closedir($dir);
    }

    protected function recursiveDelete(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (! is_dir($path)) {
            @unlink($path);
            return;
        }

        $items = scandir($path);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $target = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($target)) {
                $this->recursiveDelete($target);
            } else {
                @unlink($target);
            }
        }

        @rmdir($path);
    }
}