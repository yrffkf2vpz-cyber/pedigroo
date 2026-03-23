<?php

namespace App\Services\Normalizers\Color\Master;

class GlobalColorMap
{
    /**
     * Canonical ? variants
     *
     * Minden érték lowercase, ékezet nélkül.
     */
    public static function map(): array
    {
        return [

            // -------------------------
            // ALAPSZÍNEK
            // -------------------------
            'white' => [
                'white', 'fehér', 'feher', 'weiß', 'blanco', 'bianco'
            ],

            'black' => [
                'black', 'fekete', 'negro', 'nero', 'schwarz'
            ],

            'brown' => [
                'brown', 'barna', 'marron', 'braun', 'castano'
            ],

            'grey' => [
                'grey', 'gray', 'szürke', 'szurke', 'gris', 'grigio'
            ],

            'fawn' => [
                'fawn', 'bézs', 'beige', 'isabella', 'falb'
            ],

            // -------------------------
            // KOMPLEX SZÍNEK
            // -------------------------
            'merle' => [
                'merle', 'blue merle', 'red merle', 'marmor', 'tigriscsíkos'
            ],

            'black_tricolor' => [
                'black tricolor', 'black tri', 'fekete trikolor', 'fekete tricolor'
            ],

            'brown_tricolor' => [
                'brown tricolor', 'brown tri', 'barna trikolor'
            ],

            'golden' => [
                'golden', 'arany', 'gold', 'doré'
            ],

            'rust' => [
                'rust', 'rozsdabarna', 'rozsda'
            ],

            // -------------------------
            // BOVÍTHETO
            // -------------------------
        ];
    }
}