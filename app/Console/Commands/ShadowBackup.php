<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShadowBackup extends Command
{
    protected $signature = 'pedroo:shadow-backup';
    protected $description = 'Snapshot backup of the entire project + database into _shadow_backup';

    public function handle(): int
    {
        $projectPath = base_path(); // teljes /var/www/pedroo
        $backupRoot  = base_path('_shadow_backup');
        $timestamp   = now()->format('Y-m-d_H-i-s');
        $snapshotPath = $backupRoot . DIRECTORY_SEPARATOR . $timestamp;

        // Backup root létrehozása
        if (!is_dir($backupRoot) && !mkdir($backupRoot, 0755, true)) {
            $this->error("Cannot create backup root: {$backupRoot}");
            return self::FAILURE;
        }

        // Snapshot könyvtár létrehozása
        if (!mkdir($snapshotPath, 0755, true)) {
            $this->error("Cannot create snapshot directory: {$snapshotPath}");
            return self::FAILURE;
        }

        $this->info("Creating snapshot: {$snapshotPath}");

        // 1) Teljes projekt másolása
        $this->recursiveCopy($projectPath, $snapshotPath . '/project');

        // 2) Adatbázis dump
        $this->backupDatabase($snapshotPath . '/database.sql');

        // 3) Régi snapshotok törlése (12 óránál régebbi)
        $this->cleanupOldSnapshots($backupRoot);

        $this->info('Shadow backup completed.');
        return self::SUCCESS;
    }

    protected function recursiveCopy(string $source, string $destination): void
    {
        $dir = opendir($source);
        @mkdir($destination, 0755, true);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;

            $src = $source . DIRECTORY_SEPARATOR . $file;
            $dst = $destination . DIRECTORY_SEPARATOR . $file;

            if (is_dir($src)) {
                $this->recursiveCopy($src, $dst);
            } else {
                @copy($src, $dst);
            }
        }

        closedir($dir);
    }

    protected function backupDatabase(string $dumpFile): void
    {
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        $command = "mysqldump --user={$user} --password={$pass} {$db} > {$dumpFile}";
        exec($command);
    }

    protected function cleanupOldSnapshots(string $backupRoot): void
    {
        $files = scandir($backupRoot);
        $now = time();

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = $backupRoot . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                $ageHours = ($now - filemtime($path)) / 3600;

                if ($ageHours > 48) {
                    exec("rm -rf {$path}");
                }
            }
        }
    }
}
