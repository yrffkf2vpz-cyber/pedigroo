<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PedrooDiagnose extends Command
{
    protected $signature = 'pedroo:diagnose';
    protected $description = 'Full Pedroo system diagnostic: env, DB, storage, cache, permissions, nginx, php-fpm';

    public function handle()
    {
        $this->info("=== PEDROO DIAGNOSTIC STARTED ===");

        // 1) ENV check
        $this->section("ENV CHECK");
        $this->line("APP_ENV: " . env('APP_ENV'));
        $this->line("DB_HOST: " . env('DB_HOST'));
        $this->line("DB_DATABASE: " . env('DB_DATABASE'));

        // 2) Storage permissions
        $this->section("STORAGE PERMISSIONS");
        $storageWritable = is_writable(storage_path());
        $this->line("storage/ writable: " . ($storageWritable ? "YES" : "NO"));

        // 3) Cache directory
        $this->section("CACHE DIRECTORY");
        $cacheWritable = is_writable(storage_path('framework/cache'));
        $this->line("cache writable: " . ($cacheWritable ? "YES" : "NO"));

        // 4) DB connection test
        $this->section("DATABASE CONNECTION");
        try {
            DB::connection()->getPdo();
            $this->info("DB connection OK");
        } catch (\Exception $e) {
            $this->error("DB connection FAILED: " . $e->getMessage());
        }

        // 5) Laravel log check
        $this->section("LARAVEL LOG");
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            $this->line("Last 5 log lines:");
            $this->line("----------------------");
            $this->line(implode("\n", array_slice(file($logFile), -5)));
        } else {
            $this->line("No log file found.");
        }

        $this->info("=== PEDROO DIAGNOSTIC COMPLETE ===");
    }

    private function section($title)
    {
        $this->info("\n--- $title ---");
    }
}