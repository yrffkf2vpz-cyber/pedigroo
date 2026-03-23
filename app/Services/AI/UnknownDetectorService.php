<?php

namespace App\Services\AI;

use App\Models\LearningQueue;
use App\Models\Country;
use App\Services\Normalize\RegNoNormalizer;
use App\Services\Normalize\ColorNormalizer;
use App\Services\Health\HealthNormalizer;

class UnknownDetectorService
{
    public function detect(array $rawDog): void
    {
        $this->checkRegNo($rawDog['reg_no'] ?? null);
        $this->checkColor($rawDog['color'] ?? null);
        $this->checkHealth($rawDog['health'] ?? []);
        $this->checkCountry($rawDog['country'] ?? null);
    }

    private function checkRegNo(?string $value): void
    {
        if (!$value) {
            return;
        }

        if (!app(RegNoNormalizer::class)->canNormalize($value)) {
            $this->queue('pedroo_dogs', 'reg_no', $value, 'UNKNOWN_REG_NO');
        }
    }

    private function checkColor(?string $value): void
    {
        if (!$value) {
            return;
        }

        if (!app(ColorNormalizer::class)->canNormalize($value)) {
            $this->queue('pedroo_dogs', 'color', $value, 'UNKNOWN_COLOR');
        }
    }

    private function checkHealth(array $health): void
    {
        foreach ($health as $type => $value) {
            if (!app(HealthNormalizer::class)->canNormalizeType($type, $value)) {
                $this->queue('pedroo_health_records', $type, $value, 'UNKNOWN_HEALTH');
            }
        }
    }

    private function checkCountry(?string $value): void
    {
        if (!$value) {
            return;
        }

        if (!Country::where('code', $value)->exists()) {
            $this->queue('pedroo_dogs', 'country', $value, 'UNKNOWN_COUNTRY');
        }
    }

    private function queue(string $table, string $column, string $value, string $type): void
    {
        LearningQueue::create([
            'source_table'  => $table,
            'source_column' => $column,
            'raw_value'     => $value,
            'detected_type' => $type,
            'confidence'    => 0,
            'status'        => 'pending',
        ]);
    }
}