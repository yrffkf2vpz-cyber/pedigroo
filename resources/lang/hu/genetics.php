<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Coat Color Genetics – Genotype Names & Descriptions
    |--------------------------------------------------------------------------
    |
    | Ezek a kulcsok a ColorEngine által generált genotípusokhoz tartoznak.
    | A kulcsok formátuma: "GENE_GENOTYPE.name" és "GENE_GENOTYPE.description".
    | A ColorEngine automatikusan generálja a kulcsot: M/m ? M_m
    |
    */

    // -------------------------
    // MERLE (M-locus)
    // -------------------------
    'M_M.name'        => 'Double merle',
    'M_M.description' => 'Súlyos hallás- és látáskárosodás kockázata. A párosítás nem ajánlott.',

    'M_m.name'        => 'Merle hordozó',
    'M_m.description' => 'Merle mintázatot mutat. Csak non-merle partnerrel tenyésztheto.',

    'm_m.name'        => 'Non-merle',
    'm_m.description' => 'Nem hordoz merle gént.',


    // -------------------------
    // E-locus (recesszív vörös)
    // -------------------------
    'E_E.name'        => 'Domináns fekete',
    'E_E.description' => 'Nem hordoz recesszív vörös gént.',

    'E_e.name'        => 'Recesszív vörös hordozó',
    'E_e.description' => 'A kutya hordozza a vörös színt. A kölykök egy része vörös lehet.',

    'e_e.name'        => 'Recesszív vörös',
    'e_e.description' => 'A kutya vörös színu. Csak e/e genotípus esetén jelenik meg.',


    // -------------------------
    // B-locus (barna / red factor)
    // -------------------------
    'B_B.name'        => 'Fekete pigment',
    'B_B.description' => 'Nem hordoz barna (csoki) gént.',

    'B_b.name'        => 'Barna hordozó',
    'B_b.description' => 'A kutya fekete pigmentu, de hordozza a barna gént.',

    'b_b.name'        => 'Barna (csoki)',
    'b_b.description' => 'A kutya barna pigmentu. Csak b/b esetén jelenik meg.',


    // -------------------------
    // D-locus (dilute)
    // -------------------------
    'D_D.name'        => 'Nem hígított pigment',
    'D_D.description' => 'Nem hordoz dilute gént.',

    'D_d.name'        => 'Dilute hordozó',
    'D_d.description' => 'A kutya nem dilute, de hordozza a hígító gént.',

    'd_d.name'        => 'Dilute',
    'd_d.description' => 'A kutya hígított színu (pl. blue, lilac).',


    // -------------------------
    // S-locus (fehér jegyek)
    // -------------------------
    'S_S.name'        => 'Túlzott fehér jegyek',
    'S_S.description' => 'Megnövekedett halláskárosodás kockázata.',

    'S_s.name'        => 'Fehér jegyek hordozója',
    'S_s.description' => 'A kutya mérsékelt fehér jegyeket mutathat.',

    's_s.name'        => 'Minimális fehér jegyek',
    's_s.description' => 'A kutya alig mutat fehér jegyeket.',


    // -------------------------
    // A-locus (tanpoint / tricolor)
    // -------------------------
    'at_at.name'        => 'Tanpoint (tricolor)',
    'at_at.description' => 'A kutya tricolor mintázatot mutat.',

    'at_a.name'         => 'Tanpoint hordozó',
    'at_a.description'  => 'A kutya fekete vagy tricolor lehet, hordozza a tanpoint gént.',

    'a_a.name'          => 'Recesszív fekete',
    'a_a.description'   => 'A kutya recesszív fekete színu.',


    /*
    |--------------------------------------------------------------------------
    | Coat Color Names (ColorEngine ? színnév)
    |--------------------------------------------------------------------------
    */

    'color.blue_merle'   => 'blue merle',
    'color.red_merle'    => 'red merle',
    'color.black_tri'    => 'black tricolor',
    'color.red_tri'      => 'red tricolor',
    'color.black'        => 'fekete',
    'color.red'          => 'vörös',
    'color.blue'         => 'blue (dilute fekete)',
    'color.lilac'        => 'lilac (dilute barna)',
    'color.chocolate'    => 'barna (csoki)',
    'color.white'        => 'fehér jegyekkel',
    'color.double_merle' => 'double merle (tiltott)',


    /*
    |--------------------------------------------------------------------------
    | Generic / fallback
    |--------------------------------------------------------------------------
    */

    'NA.name'        => 'Nincs genetikai adat',
    'NA.description' => 'Ehhez a genotípushoz nem áll rendelkezésre leírás.',
];