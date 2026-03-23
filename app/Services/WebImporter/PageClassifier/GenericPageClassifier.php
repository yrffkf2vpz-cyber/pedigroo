<?php

namespace App\Services\WebImporter\PageClassifier;

use App\Services\WebImporter\Contracts\PageClassifierInterface;

class GenericPageClassifier implements PageClassifierInterface
{
    public function classify(string $html): string
    {
        $lower = mb_strtolower($html, 'UTF-8');

        if (preg_match('/(sire|dam|far|mor|apa|anya|isä|emä)/iu', $lower)) {
            return 'dog';
        }

        if (preg_match('/(pedigree|stamtavla|sukutaulu|ancestors)/iu', $lower)) {
            return 'pedigree';
        }

        return 'other';
    }
}