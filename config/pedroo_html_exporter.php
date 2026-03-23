<?php

return [

    'version' => '1.0',

    /*
    |--------------------------------------------------------------------------
    | Source directories
    |--------------------------------------------------------------------------
    | Ezekbol a mappákból olvassa a Pedroo a fájlokat.
    */

    'source_directories' => [
        'app/Services',
        'app/Models',
        'app/Http/Controllers',
        'app/Pipelines',
        'app/Handlers',
        'app/Parsers',
        'app/DTO',
        'app/Jobs',
        'app/Listeners',
    ],

    /*
    |--------------------------------------------------------------------------
    | Category mapping
    |--------------------------------------------------------------------------
    | A Pedroo automatikusan kategóriákba sorolja a fájlokat.
    */

    'categories' => [
        'services'    => 'app/Services',
        'models'      => 'app/Models',
        'controllers' => 'app/Http/Controllers',
        'pipelines'   => 'app/Pipelines',
        'handlers'    => 'app/Handlers',
        'parsers'     => 'app/Parsers',
        'dto'         => 'app/DTO',
        'jobs'        => 'app/Jobs',
        'listeners'   => 'app/Listeners',
        'legacy'      => 'legacy',
        'unknown'     => 'unknown',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTML output settings
    |--------------------------------------------------------------------------
    | A Pedroo ide generálja a HTML fájlokat.
    */

    'html_output' => [
        'root' => 'pedroo-review',   // public/pedroo-review
        'max_file_size_kb' => 250,   // max 250 KB / HTML
        'max_lines' => 2000,         // max 2000 sor / HTML
        'encoding' => 'utf-8',
        'wrap_in_pre' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing settings
    |--------------------------------------------------------------------------
    | A Pedroo emberi ritmushoz igazodva dolgozik.
    */

    'processing' => [
        'batch_size' => 1,                   // egyszerre 1 fájl
        'delay_seconds_between_exports' => 2, // 2 mp szünet két fájl között
        'order' => 'alphabetical',
        'include_metadata' => true,
        'include_hash' => true,
        'include_mtime' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Publishing settings
    |--------------------------------------------------------------------------
    | A Pedroo itt teszi közzé a HTML fájlokat.
    */

    'publishing' => [
        'base_url' => 'https://pedigroo.com/pedroo-review',
        'public_access' => true,
    ],

];