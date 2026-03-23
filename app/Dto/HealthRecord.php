<?php

namespace App\Dto;

class HealthRecord
{
    public function __construct(
        public string  $type,
        public string  $value,
        public ?string $date,
        public ?string $lab,
        public string  $source
    ) {}
}