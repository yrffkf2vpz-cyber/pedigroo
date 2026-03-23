<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PedrooScanDatabase extends Command
{
    protected $signature = 'pedroo:scan-database';
    protected $description = 'Scan database structure and store it in pd_pedroo_registry';

    public function handle(): int
    {
        $this->info('Scanning database tables...');

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $row) {
            $tableName = collect((array) $row)->first();

            // 1) T?bla be?r?sa a registry-be
            DB::table('pd_pedroo_registry')->updateOrInsert(
                [
                    'entity_type' => 'table',
                    'entity_name' => $tableName,
                ],
                [
                    'module' => $this->detectModule($tableName),
                    'status' => 'ok',
                    'details' => json_encode([
                        'columns' => [],
                        'indexes' => [],
                        'foreign_keys' => [],
                    ]),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $this->line(" - {$tableName}");

            // 2) Oszlopok be?r?sa
            $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");

            foreach ($columns as $col) {
                DB::table('pd_pedroo_registry')->updateOrInsert(
                    [
                        'entity_type' => 'column',
                        'entity_name' => "{$tableName}.{$col->Field}",
                    ],
                    [
                        'module' => $this->detectModule($tableName),
                        'status' => 'ok',
                        'details' => json_encode([
                            'table' => $tableName,
                            'type' => $col->Type,
                            'nullable' => $col->Null === 'YES',
                            'default' => $col->Default,
                            'key' => $col->Key,
                            'extra' => $col->Extra,
                        ]),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        $this->info('Database scan completed and stored in registry.');
        return self::SUCCESS;
    }

    private function detectModule(string $tableName): ?string
    {
        if (str_starts_with($tableName, 'pd_')) {
            return 'production';
        }

        if (str_starts_with($tableName, 'pedroo_')) {
            return 'sandbox';
        }

        return 'core';
    }
}