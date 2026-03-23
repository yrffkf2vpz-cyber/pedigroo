<?php

namespace App\Services\Normalizers;

use App\Models\Breed;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeBreedService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes fajta-normalizálás (globális, több kennelklub kompatibilis).
     */
    public function normalize(?string $rawBreed, ?string $country = null, bool $debug = false): array
    {
        $input = $rawBreed ? trim($rawBreed) : null;

        if (!$input) {
            return $this->result(null, null, 'empty_input', $input, [], $debug);
        }

        // ---------------------------------------------------------
        // 1) Normalizált lowercase + diakritika eltávolítás
        // ---------------------------------------------------------
        $normalized = $this->normalizeText($input);

        // ---------------------------------------------------------
        // 2) Többnyelvű aliasok (globális)
        // ---------------------------------------------------------
        $alias = $this->aliasMap();
        if (isset($alias[$normalized])) {
            return $this->finalize($alias[$normalized], $input, 'alias_match', null, null, $debug);
        }

        // ---------------------------------------------------------
        // 3) Ország-specifikus fajtanév eltérések
        // ---------------------------------------------------------
        $countryMap = $this->countrySpecificMap($country);
        if (isset($countryMap[$normalized])) {
            return $this->finalize($countryMap[$normalized], $input, 'country_specific_alias', null, null, $debug);
        }

        // ---------------------------------------------------------
        // 4) Rövidítések felismerése (GSD, APBT, JRT, etc.)
        // ---------------------------------------------------------
        $short = $this->shortCodeMap();
        if (isset($short[$normalized])) {
            return $this->finalize($short[$normalized], $input, 'shortcode_match', null, null, $debug);
        }

        // ---------------------------------------------------------
        // 5) EXACT DB MATCH
        // ---------------------------------------------------------
        $breed = Breed::whereRaw('LOWER(name) = ?', [$normalized])->first();
        if ($breed) {
            return $this->finalize($breed->name, $input, 'exact_db_match', $breed->id, null, $debug);
        }

        // ---------------------------------------------------------
        // 6) FUZZY MATCH (globális)
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchBreed($normalized);
        if ($fuzzy) {
            return $this->finalize(
                $fuzzy['canonical'],
                $input,
                'fuzzy_match',
                $fuzzy['breed_id'],
                $fuzzy['score'],
                $debug
            );
        }

        // ---------------------------------------------------------
        // 7) NINCS TALÁLAT → REVIEW
        // ---------------------------------------------------------
        return $this->result(
            canonical: null,
            breedId: null,
            reason: 'no_match',
            input: $input,
            extra: ['needs_review' => true],
            debug: $debug
        );
    }

    // ---------------------------------------------------------
    // ALIASOK (globális)
    // ---------------------------------------------------------

    private function aliasMap(): array
    {
        return [
            // German Shepherd
            'gsd' => 'german shepherd dog',
            'alsatian' => 'german shepherd dog',
            'német juhászkutya' => 'german shepherd dog',
            'deutscher schäferhund' => 'german shepherd dog',
            'berger allemand' => 'german shepherd dog',

            // American Pit Bull Terrier
            'apbt' => 'american pit bull terrier',
            'pitbull' => 'american pit bull terrier',
            'pit bull' => 'american pit bull terrier',

            // Jack Russell Terrier
            'jrt' => 'jack russell terrier',
            'jack russell' => 'jack russell terrier',

            // Border Collie
            'bc' => 'border collie',
            'border' => 'border collie',

            // Golden Retriever
            'golden' => 'golden retriever',
            'golden retr.' => 'golden retriever',

            // Labrador
            'lab' => 'labrador retriever',
            'labrador' => 'labrador retriever',

            // Chihuahua
            'chi' => 'chihuahua',
            'chihuahua long coat' => 'chihuahua',
            'chihuahua smooth coat' => 'chihuahua',

            // Poodle
            'toy poodle' => 'poodle',
            'miniature poodle' => 'poodle',
            'standard poodle' => 'poodle',
        ];
    }

    private function shortCodeMap(): array
    {
        return [
            'gsd' => 'german shepherd dog',
            'apbt' => 'american pit bull terrier',
            'jrt' => 'jack russell terrier',
            'bc' => 'border collie',
            'gr' => 'golden retriever',
            'lr' => 'labrador retriever',
            'chi' => 'chihuahua',
            'poodle' => 'poodle',
        ];
    }

    private function countrySpecificMap(?string $country): array
    {
        $country = strtolower((string)$country);

        return match ($country) {
            'de', 'germany' => [
                'schäferhund' => 'german shepherd dog',
            ],
            'fr', 'france' => [
                'berger' => 'german shepherd dog',
            ],
            'hu', 'hungary' => [
                'juhászkutya' => 'german shepherd dog',
            ],
            default => [],
        };
    }

    // ---------------------------------------------------------
    // EREDMÉNY
    // ---------------------------------------------------------

    private function finalize(
        string $canonical,
        string $input,
        string $reason,
        ?int $breedId = null,
        ?float $score = null,
        bool $debug = false
    ): array {
        if (!$breedId) {
            $breedId = Breed::where('name', $canonical)->value('id');
        }

        return $this->result(
            canonical: $canonical,
            breedId: $breedId,
            reason: $reason,
            input: $input,
            extra: ['score' => $score],
            debug: $debug
        );
    }

    private function result(
        ?string $canonical,
        ?int $breedId,
        string $reason,
        ?string $input,
        array $extra = [],
        bool $debug = false
    ): array {
        $base = [
            'breed'    => $canonical,
            'breed_id' => $breedId,
        ];

        if ($debug) {
            $base['debug'] = array_merge([
                'input'     => $input,
                'canonical' => $canonical,
                'breed_id'  => $breedId,
                'reason'    => $reason,
            ], $extra);
        }

        return $base;
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $text = strtr($text, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','Ő'=>'o','Ú'=>'u','Ü'=>'u','Ű'=>'u',
        ]);

        return preg_replace('/\s+/', ' ', $text);
    }
}