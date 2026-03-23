<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeColorService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes szín-normalizálás (globális, fajtaspecifikus, genetikai).
     */
    public function normalize(array $raw, bool $debug = false): array
    {
        $input   = $raw['raw_color']   ?? null;
        $breed   = $raw['raw_breed']   ?? null;
        $country = $raw['raw_country'] ?? null;

        if (!$input) {
            return $this->result(null, $input, 'empty_input', [], $debug);
        }

        $clean = $this->normalizeText($input);

        // ---------------------------------------------------------
        // 1) GENETIKAI SZÍNKÓD (Ay/at, B/b, E/e, D/d, K/k, M/m…)
        // ---------------------------------------------------------
        if ($gen = $this->detectGeneticCode($clean)) {
            return $this->result($gen, $input, 'genetic_code', ['genetic' => true], $debug);
        }

        // ---------------------------------------------------------
        // 2) FAJTASPECIFIKUS SZÍNMAP (globális)
        // ---------------------------------------------------------
        $breedMap = $this->breedSpecificMap($breed);
        if (isset($breedMap[$clean])) {
            return $this->result($breedMap[$clean], $input, 'breed_specific', [], $debug);
        }

        // ---------------------------------------------------------
        // 3) ORSZÁG-SPECIFIKUS SZÍNMAP
        // ---------------------------------------------------------
        $countryMap = $this->countrySpecificMap($country);
        if (isset($countryMap[$clean])) {
            return $this->result($countryMap[$clean], $input, 'country_specific', [], $debug);
        }

        // ---------------------------------------------------------
        // 4) GLOBÁLIS ALIASOK
        // ---------------------------------------------------------
        $alias = $this->globalAliasMap();
        if (isset($alias[$clean])) {
            return $this->result($alias[$clean], $input, 'global_alias', [], $debug);
        }

        // ---------------------------------------------------------
        // 5) SZÍNMINTÁK (merle, brindle, sable, harlequin, mantle…)
        // ---------------------------------------------------------
        if ($pattern = $this->detectPattern($clean)) {
            return $this->result($pattern, $input, 'pattern_detected', [], $debug);
        }

        // ---------------------------------------------------------
        // 6) ADATBÁZIS EXACT MATCH (pd_breed_colors)
        // ---------------------------------------------------------
        $match = DB::table('pd_breed_colors')
            ->whereRaw('LOWER(name) = ?', [$clean])
            ->value('name');

        if ($match) {
            return $this->result($match, $input, 'exact_db_match', [], $debug);
        }

        // ---------------------------------------------------------
        // 7) FUZZY MATCH
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchColor($clean, $breed);
        if ($fuzzy) {
            return $this->result(
                canonical: $fuzzy['canonical'],
                input: $input,
                reason: 'fuzzy_match',
                extra: ['score' => $fuzzy['score']],
                debug: $debug
            );
        }

        // ---------------------------------------------------------
        // 8) LEARNING QUEUE
        // ---------------------------------------------------------
        $learned = $this->learningQueue($clean);

        return $this->result(
            canonical: $clean,
            input: $input,
            reason: 'learning',
            extra: ['learned' => $learned],
            debug: $debug
        );
    }

    // ---------------------------------------------------------
    // CANONICAL RESULT
    // ---------------------------------------------------------

    private function result(?string $canonical, ?string $input, string $reason, array $extra = [], bool $debug = false): array
    {
        $base = [
            'color'          => $canonical,
            'official_color' => $canonical,
            'birth_color'    => $canonical,
        ];

        if ($debug) {
            $base['debug'] = array_merge([
                'input'     => $input,
                'canonical' => $canonical,
                'reason'    => $reason,
            ], $extra);
        }

        return $base;
    }

    // ---------------------------------------------------------
    // NORMALIZÁLÁS
    // ---------------------------------------------------------

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $text = strtr($text, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','o'=>'o','ú'=>'u','ü'=>'u','u'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','O'=>'o','Ú'=>'u','Ü'=>'u','U'=>'u',
        ]);

        return preg_replace('/\s+/', ' ', $text);
    }

    // ---------------------------------------------------------
    // GENETIKAI SZÍNKÓD
    // ---------------------------------------------------------

    private function detectGeneticCode(string $clean): ?string
    {
        if (preg_match('/^[A-Za-z]{1,2}\/?[A-Za-z]{1,2}$/', $clean)) {
            return strtoupper($clean);
        }
        return null;
    }

    // ---------------------------------------------------------
    // FAJTASPECIFIKUS SZÍNMAP
    // ---------------------------------------------------------

    private function breedSpecificMap(?string $breed): array
    {
        $breed = mb_strtolower((string)$breed);

        return match ($breed) {
            'border collie' => [
                'blue merle'  => 'blue merle',
                'red merle'   => 'red merle',
                'black white' => 'black & white',
                'black & white' => 'black & white',
                'tricolor'    => 'tricolor',
            ],
            'australian shepherd' => [
                'blue merle' => 'blue merle',
                'red merle'  => 'red merle',
                'black tri'  => 'black tricolor',
                'red tri'    => 'red tricolor',
            ],
            'german shepherd dog' => [
                'fekete'      => 'black',
                'fekete cser' => 'black & tan',
                'sable'       => 'sable',
            ],
            default => [],
        };
    }

    // ---------------------------------------------------------
    // ORSZÁG-SPECIFIKUS SZÍNMAP
    // ---------------------------------------------------------

    private function countrySpecificMap(?string $country): array
    {
        $country = mb_strtolower((string)$country);

        return match ($country) {
            'de', 'germany' => [
                'schwarz' => 'black',
                'braun'   => 'brown',
                'blau'    => 'blue',
            ],
            'fr', 'france' => [
                'noir'   => 'black',
                'marron' => 'brown',
                'bleu'   => 'blue',
            ],
            'hu', 'hungary' => [
                'fekete' => 'black',
                'barna'  => 'brown',
                'kek'    => 'blue',
            ],
            default => [],
        };
    }

    // ---------------------------------------------------------
    // GLOBÁLIS ALIASOK
    // ---------------------------------------------------------

    private function globalAliasMap(): array
    {
        return [
            'blk' => 'black',
            'brn' => 'brown',
            'blu' => 'blue',
            'wht' => 'white',
            'gry' => 'grey',
            'grizzle' => 'grizzle',
            'tri' => 'tricolor',
            'tri color' => 'tricolor',
            'tri-colour' => 'tricolor',
        ];
    }

    // ---------------------------------------------------------
    // SZÍNMINTÁK (pattern detection)
    // ---------------------------------------------------------

    private function detectPattern(string $clean): ?string
    {
        $patterns = [
            'merle'     => 'merle',
            'brindle'   => 'brindle',
            'sable'     => 'sable',
            'harlequin' => 'harlequin',
            'mantle'    => 'mantle',
            'piebald'   => 'piebald',
            'ticking'   => 'ticked',
            'ticked'    => 'ticked',
        ];

        foreach ($patterns as $k => $v) {
            if (str_contains($clean, $k)) {
                return $v;
            }
        }

        return null;
    }

    // ---------------------------------------------------------
    // LEARNING QUEUE
    // ---------------------------------------------------------

    private function learningQueue(string $clean): bool
    {
        $existing = DB::table('pd_color_learning_queue')
            ->where('raw_input', $clean)
            ->first();

        if ($existing) {
            $newCount = $existing->count + 1;

            DB::table('pd_color_learning_queue')
                ->where('id', $existing->id)
                ->update([
                    'count'       => $newCount,
                    'last_seen_at'=> now(),
                ]);

            if ($newCount >= 3 && $existing->status === 'NEW') {
                DB::table('pd_color_learning_queue')
                    ->where('id', $existing->id)
                    ->update(['status' => 'CONFIRMED']);

                DB::table('pd_breed_colors_aliases')->insertOrIgnore([
                    'alias'     => $clean,
                    'canonical' => $clean,
                ]);

                return true;
            }

            return false;
        }

        DB::table('pd_color_learning_queue')->insert([
            'raw_input'        => $clean,
            'normalized_input' => $clean,
            'first_seen_at'    => now(),
            'last_seen_at'     => now(),
            'count'            => 1,
            'status'           => 'NEW',
        ]);

        return false;
    }
}