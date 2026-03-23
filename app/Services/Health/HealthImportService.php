<?php

namespace App\Services\Health;

use App\Models\HealthRecord;

class HealthImportService
{
    public function import(string $dogId, array $records): void
    {
        foreach ($records as $record) {
            HealthRecord::create([
                'dog_id' => $dogId,
                'type'   => $record->type,
                'value'  => $record->value,
                'date'   => $record->date,
                'lab'    => $record->lab,
                'source' => $record->source,
            ]);
        }
    }
}