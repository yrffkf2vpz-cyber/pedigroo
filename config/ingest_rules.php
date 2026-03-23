<?php

return [

    // Többnyelvű mezőnév felismerés
    'fields' => [
        'name' => [
            'name', 'dog name', 'név', 'jméno psa', 'meno psa'
        ],
        'reg_no' => [
            'reg no', 'registration', 'regisztrációs szám', 'zuchtbuchnummer'
        ],
        'color' => [
            'color', 'szín', 'farbe', 'couleur'
        ],
        'birth_date' => [
            'birth date', 'születési dátum', 'geburtsdatum', 'date de naissance'
        ],
        'breeder' => [
            'breeder', 'tenyésztő', 'züchter', 'éleveur'
        ],
    ],

    // Forrás-specifikus szabályok
    'sources' => [
        'excel' => [
            'trim_whitespace' => true,
            'normalize_headers' => true,
        ],
        'pdf' => [
            'ocr_cleanup' => true,
        ],
        'api' => [
            'strict_types' => true,
        ],
    ],
];