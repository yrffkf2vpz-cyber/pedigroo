<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Coat Color Genetics – Genotype Names & Descriptions
    |--------------------------------------------------------------------------
    |
    | Keys follow the pattern:
    |   GENOTYPE.name
    |   GENOTYPE.description
    |
    | The ColorEngine generates keys automatically:
    |   "M/m" → "M_m"
    |   "e/e" → "e_e"
    |   "at/at" → "at_at"
    |
    */

    // -------------------------
    // MERLE (M-locus)
    // -------------------------
    'M_M.name'        => 'Double merle',
    'M_M.description' => 'Severe risk of hearing and vision impairment. Breeding not recommended.',

    'M_m.name'        => 'Merle',
    'M_m.description' => 'Merle pattern. Should only be bred to non-merle partners.',

    'm_m.name'        => 'Non-merle',
    'm_m.description' => 'Does not carry the merle gene.',


    // -------------------------
    // E-locus (recessive red)
    // -------------------------
    'E_E.name'        => 'Dominant black',
    'E_E.description' => 'Does not carry the recessive red gene.',

    'E_e.name'        => 'Red carrier',
    'E_e.description' => 'Carries the recessive red gene. Offspring may be red.',

    'e_e.name'        => 'Recessive red',
    'e_e.description' => 'Expresses red coat color. Only appears in e/e dogs.',


    // -------------------------
    // B-locus (brown / chocolate)
    // -------------------------
    'B_B.name'        => 'Black pigment',
    'B_B.description' => 'Does not carry the brown (chocolate) gene.',

    'B_b.name'        => 'Brown carrier',
    'B_b.description' => 'Carries the brown gene. Offspring may be chocolate.',

    'b_b.name'        => 'Brown (chocolate)',
    'b_b.description' => 'Expresses brown/chocolate pigment.',


    // -------------------------
    // D-locus (dilute)
    // -------------------------
    'D_D.name'        => 'Full pigment',
    'D_D.description' => 'Does not carry the dilute gene.',

    'D_d.name'        => 'Dilute carrier',
    'D_d.description' => 'Carries the dilute gene. Offspring may be dilute.',

    'd_d.name'        => 'Dilute',
    'd_d.description' => 'Diluted pigment (e.g., blue, lilac).',


    // -------------------------
    // S-locus (white spotting)
    // -------------------------
    'S_S.name'        => 'Excessive white spotting',
    'S_S.description' => 'Increased risk of congenital deafness.',

    'S_s.name'        => 'White spotting carrier',
    'S_s.description' => 'May show moderate white markings.',

    's_s.name'        => 'Minimal white',
    's_s.description' => 'Shows little to no white spotting.',


    // -------------------------
    // A-locus (tanpoint / tricolor)
    // -------------------------
    'at_at.name'        => 'Tanpoint (tricolor)',
    'at_at.description' => 'Expresses tanpoint/tricolor pattern.',

    'at_a.name'         => 'Tanpoint carrier',
    'at_a.description'  => 'May be black or tricolor. Carries tanpoint.',

    'a_a.name'          => 'Recessive black',
    'a_a.description'   => 'Expresses recessive black coat color.',


    /*
    |--------------------------------------------------------------------------
    | Coat Color Names (ColorEngine → phenotype names)
    |--------------------------------------------------------------------------
    */

    'color.blue_merle'   => 'blue merle',
    'color.red_merle'    => 'red merle',
    'color.black_tri'    => 'black tricolor',
    'color.red_tri'      => 'red tricolor',
    'color.black'        => 'black',
    'color.red'          => 'red',
    'color.blue'         => 'blue (dilute black)',
    'color.lilac'        => 'lilac (dilute brown)',
    'color.chocolate'    => 'chocolate (brown)',
    'color.white'        => 'white markings',
    'color.double_merle' => 'double merle (not recommended)',


    /*
    |--------------------------------------------------------------------------
    | Health & Disease Genetics (existing keys)
    |--------------------------------------------------------------------------
    */

    'PRA.name'        => 'Progressive Retinal Atrophy',
    'PRA.description' => 'Carrier of PRA: increased risk of progressive vision loss and eventual blindness.',

    'OI.name'         => 'Osteogenesis Imperfecta',
    'OI.description'  => 'Carrier of bone development disorder: increased bone fragility and fracture risk.',

    'Ridge.name'        => 'Ridge Gene',
    'Ridge.description' => 'Carrier of ridge-related mutation: risk of absent or exaggerated dorsal ridge and associated defects.',

    'HUU.name'        => 'Hyperuricosuria',
    'HUU.description' => 'Carrier of hyperuricosuria: increased risk of urinary stone formation.',

    'CYS.name'        => 'Cystinuria',
    'CYS.description' => 'Carrier of cystinuria: risk of cystine urinary stones and urinary tract issues.',

    'CA.name'         => 'Cerebellar Ataxia',
    'CA.description'  => 'Carrier of cerebellar ataxia: risk of coordination, balance and movement disorders.',

   