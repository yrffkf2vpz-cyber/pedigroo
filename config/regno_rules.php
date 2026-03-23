<?php

return [

    // Magyarország
    'HU' => [
        'patterns' => [
            // XY-1234/24
            '/^[A-Z]{2}-\d{4}\/\d{2}$/' => 'mke',
            // 1234/2024
            '/^\d{4}\/\d{4}$/'          => 'mkfe',
        ],
    ],

    // Szlovákia – példa
    'SK' => [
        'patterns' => [
            // SKJ12345
            '/^SKJ\d{5}$/' => 'skj',
        ],
    ],

    // Németország – példa
    'DE' => [
        'patterns' => [
            // VDH 123456
            '/^VDH\s?\d{4,6}$/' => 'vdh',
        ],
    ],

    // Franciaország – példa
    'FR' => [
        'patterns' => [
            // LOF 123456
            '/^LOF\s?\d{4,6}$/' => 'lof',
        ],
    ],

    // USA – példa
    'US' => [
        'patterns' => [
            // AKC DN12345678
            '/^AKC\s?[A-Z]{2}\d{6,8}$/' => 'akc',
            // UKC P123-456
            '/^UKC\s?[A-Z]\d{3}-\d{3}$/' => 'ukc',
        ],
    ],

];