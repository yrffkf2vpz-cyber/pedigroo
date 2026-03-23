<?php

namespace App\Services\RuleEngine;

class RuleEngineResult
{
    public function __construct(
        public array $results
    ) {}

    public function passed(): bool
    {
        foreach ($this->results as $r) {
            if (!$r['passed']) {
                return false;
            }
        }
        return true;
    }

    public function failedRules(): array
    {
        return array_filter($this->results, fn ($r) => !$r['passed']);
    }
}