<?php

return [

    // Pipeline sorrend
    'pipeline' => [
        'country',
        'reg_no',
        'name',
        'kennel',
        'color',
        'health',
    ],

    // Debug mód
    'debug' => env('NORMALIZE_DEBUG', false),

    // TEMP → REG összeolvadás
    'merge_temp_into_reg' => true,

    // Strict / loose mód
    'strict' => false,
];