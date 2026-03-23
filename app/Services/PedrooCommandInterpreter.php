<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class PedrooCommandInterpreter
{
    public function handle(string $command): string
    {
        // Egyszeru p?lda: mezok hozz?ad?sa a dogs t?bl?hoz
        if (preg_match('/dogs/i', $command) && preg_match('/mezo/i', $command)) {
            $fields = $this->extractFields($command);
            return $this->addColumns('dogs', $fields);
        }

        return "Nem ?rtem a parancsot.";
    }

    protected function extractFields(string $text): array
    {
        preg_match_all('/"([^"]+)"/', $text, $matches);
        return $matches[1] ?? [];
    }

    protected function addColumns(string $table, array $fields): string
    {
        if (empty($fields)) {
            return "Nem tal?ltam mezoneveket.";
        }

        $migrationName = 'add_fields_to_' . $table . '_' . time();

        Artisan::call('make:migration', [
            'name' => $migrationName,
            '--table' => $table,
        ]);

        return "Migration l?trehozva: {$migrationName}, mezok: " . implode(', ', $fields);
    }
}
