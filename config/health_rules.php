<?php

return [

    // HD – Hip Dysplasia
    'HD' => [
        'patterns' => [
            '/^hd\s*a$/i' => 'A',
            '/^hd\s*b$/i' => 'B',
            '/^hd\s*c$/i' => 'C',
            '/^hd\s*d$/i' => 'D',
            '/^hd\s*e$/i' => 'E',
        ],
        'fallback' => 'UNKNOWN',
    ],

    // ED – Elbow Dysplasia
    'ED' => [
        'patterns' => [
            '/^ed\s*0\/0$/i' => '0/0',
            '/^ed\s*0\/1$/i' => '0/1',
            '/^ed\s*1\/1$/i' => '1/1',
            '/^ed\s*1\/2$/i' => '1/2',
            '/^ed\s*2\/2$/i' => '2/2',
        ],
        'fallback' => 'UNKNOWN',
    ],

    // DM – Degenerative Myelopathy
    'DM' => [
        'patterns' => [
            '/^n\/n$/i' => 'N/N',
            '/^n\/a$/i' => 'N/A',
            '/^a\/a$/i' => 'A/A',
            '/^clear$/i' => 'N/N',
            '/^carrier$/i' => 'N/A',
            '/^affected$/i' => 'A/A',
        ],
        'fallback' => 'UNKNOWN',
    ],

    // MDR1
    'MDR1' => [
        'patterns' => [
            '/^n\/n$/i' => 'N/N',
            '/^n\/m$/i' => 'N/M',
            '/^m\/m$/i' => 'M/M',
        ],
        'fallback' => 'UNKNOWN',
    ],

    // PRA, HUU, PL, OCD stb.
    'GENERIC' => [
        'clear'    => ['clear', 'free', 'n/n'],
        'carrier'  => ['carrier', 'n/a', 'het'],
        'affected' => ['affected', 'a/a', 'mut/mut'],
        'fallback' => 'UNKNOWN',
    ],
];