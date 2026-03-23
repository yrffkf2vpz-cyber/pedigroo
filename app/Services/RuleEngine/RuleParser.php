<?php

namespace App\Services\RuleEngine;

class RuleParser
{
    public function parse(array|string $json): array
    {
        if (is_string($json)) {
            $json = json_decode($json, true);
        }

        return [
            'field' => $json['field'],
            'operator' => $json['operator'],
            'value' => $json['value'] ?? null,
        ];
    }
}