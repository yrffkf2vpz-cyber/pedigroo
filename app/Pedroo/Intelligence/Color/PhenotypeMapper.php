<?php

namespace App\Pedroo\Intelligence\Color;

class PhenotypeMapper
{
    /**
     * Genotype profile → phenotype color key
     *
     * @param array $genotypes ['M' => 'M/m', 'E' => 'E/e', ...]
     * @return string color.* translation key
     */
    public function map(array $genotypes): string
    {
        // 1) Merle
        if (isset($genotypes['M'])) {
            if ($genotypes['M'] === 'M/M') {
                return 'color.double_merle';
            }
            if ($genotypes['M'] === 'M/m') {
                // red merle vs blue merle
                if (isset($genotypes['E']) && $genotypes['E'] === 'e/e') {
                    return 'color.red_merle';
                }
                return 'color.blue_merle';
            }
        }

        // 2) Recessive red
        if (isset($genotypes['E']) && $genotypes['E'] === 'e/e') {
            return 'color.red';
        }

        // 3) Tanpoint / tricolor
        if (isset($genotypes['A'])) {
            if ($genotypes['A'] === 'at/at' || $genotypes['A'] === 'at/a') {
                // dilute tricolor?
                if (isset($genotypes['D']) && $genotypes['D'] === 'd/d') {
                    return 'color.blue'; // dilute black tri
                }
                return 'color.black_tri';
            }
            if ($genotypes['A'] === 'a/a') {
                return 'color.black';
            }
        }

        // 4) Brown (chocolate)
        if (isset($genotypes['B']) && $genotypes['B'] === 'b/b') {
            // dilute chocolate?
            if (isset($genotypes['D']) && $genotypes['D'] === 'd/d') {
                return 'color.lilac';
            }
            return 'color.chocolate';
        }

        // 5) Dilute (blue)
        if (isset($genotypes['D']) && $genotypes['D'] === 'd/d') {
            return 'color.blue';
        }

        // 6) White spotting (modifier)
        if (isset($genotypes['S']) && $genotypes['S'] === 'S/S') {
            return 'color.white';
        }

        // Fallback
        return 'color.black';
    }
}