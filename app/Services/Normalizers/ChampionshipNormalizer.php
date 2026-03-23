<?php

namespace App\Services\Normalizers;

use App\Services\Title\TitleNormalizer;
use Illuminate\Support\Facades\DB;

class ChampionshipNormalizer
{
    protected TitleNormalizer $titleNormalizer;

    public function __construct(TitleNormalizer $titleNormalizer)
    {
        $this->titleNormalizer = $titleNormalizer;
    }

    /**
     * Normalize a raw championship record before inserting into sandbox.
     *
     * @param array $row
     * @return array
     */
    public function normalize(array $row): array
    {
        // 1) DOG NAME NORMALIZÁLÁS
        $dogName = $this->normalizeDogName($row['dog_name'] ?? null);

        // 2) EVENT NORMALIZÁLÁS
        $eventName = $this->normalizeEventName($row['event_name'] ?? null);

        // 3) TITLE NORMALIZÁLÁS
        $titleCode = $this->normalizeTitleCode($row['title_code'] ?? null);
        $titleName = $row['title_name'] ?? null;

        // 4) COUNTRY NORMALIZÁLÁS
        $country = $this->normalizeCountry($row['country'] ?? null);

        // 5) DATE NORMALIZÁLÁS
        $date = $this->normalizeDate($row['date'] ?? null);

        // 6) TITLE DEFINITION ID FELISMERÉSE
        $titleDefinitionId = $this->titleNormalizer->normalize(
            $titleCode ?? $titleName ?? '',
            $country
        );

        return [
            'dog_name'            => $dogName,
            'event_name'          => $eventName,
            'title_code'          => $titleCode,
            'title_name'          => $titleName,
            'title_definition_id' => $titleDefinitionId,
            'country'             => $country,
            'date'                => $date,
            'source'              => $row['source'] ?? null,
            'external_id'         => $row['external_id'] ?? null,
            'confidence'          => $row['confidence'] ?? 100,
        ];
    }

    protected function normalizeDogName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }

    protected function normalizeEventName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }

    protected function normalizeTitleCode(?string $code): ?string
    {
        if (!$code) {
            return null;
        }

        $code = strtoupper(trim($code));

        // Példák:
        // "Ch" → "CH"
        // "jch" → "JCH"
        // "INT.CH" → "INT CH"

        $code = str_replace(['.', '-', '_'], ' ', $code);
        $code = preg_replace('/\s+/', ' ', $code);

        return $code;
    }

    protected function normalizeCountry(?string $country): ?string
    {
        if (!$country) {
            return null;
        }

        $country = strtoupper(trim($country));

        // Példák:
        // "hun" → "HU"
        // "HUN" → "HU"
        // "USA" → "US"

        $map = [
            'HUN' => 'HU',
            'USA' => 'US',
            'GBR' => 'GB',
            'GER' => 'DE',
        ];

        return $map[$country] ?? $country;
    }

    protected function normalizeDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        // Formátumok:
        // 2024-01-15
        // 15/01/2024
        // 15.01.2024
        // Jan 15 2024

        $date = trim($date);

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd.m.Y',
            'M d Y',
            'd M Y',
        ];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $date);
            if ($parsed) {
                return $parsed->format('Y-m-d');
            }
        }

        return null;
    }
}