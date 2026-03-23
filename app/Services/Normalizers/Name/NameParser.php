<?php

namespace App\Services\Normalizers\Name;

use App\Services\Normalizers\AdvancedNameParser;

class NameParser
{
    protected AdvancedNameParser $parser;

    public function __construct()
    {
        $this->parser = new AdvancedNameParser();
    }

    public function parse(string $rawName): array
    {
        // Megh?vjuk a r?gi, stdClass-t visszaad? parsert
        $parsed = $this->parser->parse($rawName);

        // Ha stdClass, alak?tsuk ?t array-re
        if ($parsed instanceof \stdClass) {
            $parsed = (array) $parsed;
        }

        // Garant?ljuk az array strukt?r?t
        return [
            'first_name' => $parsed['first_name'] ?? null,
            'last_name'  => $parsed['last_name'] ?? null,
            'full_name'  => trim($rawName),
        ];
    }
}