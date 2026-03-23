<?php

return [

    'DE' => [
        // vom, von, aus
        '/\b(vom|von|aus)\b/i',
    ],

    'CZ' => [
        // z, ze, od, u – ezeket később finomíthatjuk
        '/\b(z|ze|od|u)\b/i',
    ],

    'SK' => [
        '/\b(z|zo|od)\b/i',
    ],

    'FR' => [
        // de, du, des
        '/\b(de|du|des)\b/i',
    ],

    'IT' => [
        // della, del, dei
        '/\b(della|del|dei)\b/i',
    ],

    'PT' => [
        // da, do
        '/\b(da|do)\b/i',
    ],

    'NL' => [
        // van, vande, vander
        '/\b(van|vande|vander)\b/i',
    ],

    'HU' => [
        // „-i” végződés, nagyon óvatosan (pl. „Tiszai”, „Dunai”)
        '/[a-záéíóöőúüű]+i$/iu',
    ],

];