<?php

namespace App\Services\Title;

use Illuminate\Support\Facades\DB;

class TitleNormalizer
{
    protected TitleDefinitionService $definitions;

    public function __construct(TitleDefinitionService $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Normalize a raw title string into a canonical title_definitions.id
     *
     * @param string $rawTitle   e.g. "HUN CH", "CH HU", "Hungary Champion", "CH"
     * @param string|null $countryCode  e.g. "HU", "US", "DE"
     * @return int|null
     */
    public function normalize(string $rawTitle, ?string $countryCode = null): ?int
    {
        $raw = trim($rawTitle);

        // 1) Canonicalizálás
        $normalized = $this->canonicalize($raw);

        // 2) Ország ID lekérése
        $countryId = $this->resolveCountryId($countryCode);

        // 3) Kísérlet: title_code + country_id alapján
        if ($countryId && $normalized['code']) {
            $match = DB::table('title_definitions')
                ->where('country_id', $countryId)
                ->where('title_code', $normalized['code'])
                ->first();

            if ($match) {
                return $match->id;
            }
        }

        // 4) Kísérlet: title_name alapján (pl. "Hungary Champion")
        if ($countryId && $normalized['name']) {
            $match = DB::table('title_definitions')
                ->where('country_id', $countryId)
                ->where('title_name', 'LIKE', '%' . $normalized['name'] . '%')
                ->first();

            if ($match) {
                return $match->id;
            }
        }

        // 5) Fallback: csak title_code alapján (ország nélkül)
        if ($normalized['code']) {
            $match = DB::table('title_definitions')
                ->where('title_code', $normalized['code'])
                ->first();

            if ($match) {
                return $match->id;
            }
        }

        // 6) Fallback: csak title_name alapján
        if ($normalized['name']) {
            $match = DB::table('title_definitions')
                ->where('title_name', 'LIKE', '%' . $normalized['name'] . '%')
                ->first();

            if ($match) {
                return $match->id;
            }
        }

        // 7) Nem található
        return null;
    }

    /**
     * Canonicalize raw title strings.
     */
    protected function canonicalize(string $raw): array
    {
        $upper = mb_strtoupper($raw);

        // Példák:
        // "HUN CH" → code: CH, name: null
        // "CH HU" → code: CH
        // "Hungary Champion" → name: "Champion"
        // "CH" → code: CH

        // Title code felismerése
        preg_match('/\b(CH|JCH|INT CH|GR CH|VCH|WCH)\b/u', $upper, $codeMatch);

        $code = $codeMatch[1] ?? null;

        // Title name felismerése (Champion, Junior Champion, stb.)
        $name = null;
        if (str_contains($upper, 'CHAMPION')) {
            $name = 'Champion';
        }

        return [
            'code' => $code,
            'name' => $name,
        ];
    }

    /**
     * Resolve country code → country_id
     */
    protected function resolveCountryId(?string $countryCode): ?int
    {
        if (!$countryCode) {
            return null;
        }

        return DB::table('countries')
            ->where('code', strtoupper($countryCode))
            ->value('id');
    }
}