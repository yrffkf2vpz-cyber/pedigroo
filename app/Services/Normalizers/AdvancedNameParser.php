<?php

namespace App\Services\Normalizers;

use App\Services\Normalizers\Rules\PrefixRules;
use App\Services\Normalizers\Rules\SuffixRules;
use App\Services\Normalizers\Rules\CleanupRules;
use App\Services\Normalizers\Rules\KennelRules;
use App\Services\Normalizers\Rules\RegNoRules;

class AdvancedNameParser
{
    protected array $titlePatterns = [
        'CH', 'JCH', 'INT CH', 'MULTI CH', 'GRAND CH',
        'WORLD WINNER', 'EURO WINNER',
    ];

    protected array $prefixWords = [];
    protected array $suffixPatterns = [];
    protected array $cleanupAccents = [];
    protected array $cleanupSpecial = [];

    public function __construct()
    {
        $this->prefixWords     = PrefixRules::list();
        $this->suffixPatterns  = SuffixRules::list();
        $this->cleanupAccents  = CleanupRules::accents();
        $this->cleanupSpecial  = CleanupRules::specialCharacters();
    }

    /**
     * ?J: debug param?ter + teljes debug strukt?ra
     */
    public function parse(string $rawName, ?string $country = null, bool $debug = false): object
    {
        $rawNameOriginal = $rawName;

        // ---------------------------------------------------------
        // 1) REGNO KIV?G?SA
        // ---------------------------------------------------------
        $regNo = $this->extractRegNo($rawName);

        // ---------------------------------------------------------
        // 2) CLEANUP
        // ---------------------------------------------------------
        $cleaned = $this->cleanup($rawName);

        // ---------------------------------------------------------
        // 3) TOKENIZ?L?S
        // ---------------------------------------------------------
        $tokens = $this->tokenize($cleaned);

        // ---------------------------------------------------------
        // 4) C?MEK
        // ---------------------------------------------------------
        [$tokensWithoutTitles, $titles] = $this->extractTitles($tokens);

        // ---------------------------------------------------------
        // 5) PREFIX
        // ---------------------------------------------------------
        [$tokensAfterPrefix, $kennelPrefixTokens] = $this->extractKennelPrefix($tokensWithoutTitles);

        // ---------------------------------------------------------
        // 6) SUFFIX
        // ---------------------------------------------------------
        [$coreTokens, $kennelSuffixTokens] = $this->extractKennelSuffix($tokensAfterPrefix);

        // ---------------------------------------------------------
        // 7) N?V MAG
        // ---------------------------------------------------------
        $name = $this->buildNameFromTokens($coreTokens);

        // ---------------------------------------------------------
        // 8) NORMALIZ?LT FORM?K
        // ---------------------------------------------------------
        $kennelPrefix = $this->normalizeNameFromTokens($kennelPrefixTokens);
        $kennelSuffix = $this->normalizeNameFromTokens($kennelSuffixTokens);

        // ---------------------------------------------------------
        // 9) DEBUG STRUKT?RA
        // ---------------------------------------------------------
        $debugData = $debug ? [
            'raw_input' => $rawNameOriginal,
            'country'   => $country,

            'cleanup' => [
                'before' => $rawNameOriginal,
                'after'  => $cleaned,
            ],

            'tokens' => [
                'all' => $tokens,
                'without_titles' => $tokensWithoutTitles,
                'after_prefix' => $tokensAfterPrefix,
                'core' => $coreTokens,
            ],

            'parsed' => [
                'kennel_prefix' => $kennelPrefix,
                'dog_name'      => $name,
                'kennel_suffix' => $kennelSuffix,
                'titles'        => $titles,
            ],

            'regno' => [
                'raw' => $regNo,
            ],

            'rules' => [
                'prefix_words' => $this->prefixWords,
                'suffix_patterns' => $this->suffixPatterns,
            ],

            'errors' => null,
        ] : null;

        // ---------------------------------------------------------
        // 10) V?GSO OBJEKTUM
        // ---------------------------------------------------------
        return (object)[
            'kennel_prefix' => $kennelPrefix,
            'dog_name'      => $name,
            'kennel_suffix' => $kennelSuffix,
            'titles'        => $titles,
            'owners'        => [], // k?sobb bov?tj?k
            'ai_used'       => false,
            'ai_prompt'     => null,
            'ai_output'     => null,
            'debug'         => $debugData,
        ];
    }

    protected function cleanup(string $name): string
    {
        $name = strtr($name, $this->cleanupAccents);
        $name = str_replace($this->cleanupSpecial, ' ', $name);
        return preg_replace('/\s+/', ' ', $name);
    }

    protected function tokenize(string $name): array
    {
        return array_values(array_filter(explode(' ', $name), fn ($t) => $t !== ''));
    }

    protected function extractTitles(array $tokens): array
    {
        $titles = [];
        $remaining = [];

        $i = 0;
        while ($i < count($tokens)) {
            $current = $tokens[$i];
            $next    = $tokens[$i + 1] ?? null;

            $combined = $next ? $current . ' ' . $next : null;

            if ($combined && $this->isTitle($combined)) {
                $titles[] = $combined;
                $i += 2;
                continue;
            }

            if ($this->isTitle($current)) {
                $titles[] = $current;
                $i++;
                continue;
            }

            $remaining[] = $current;
            $i++;
        }

        return [$remaining, $titles];
    }

    protected function isTitle(string $token): bool
    {
        $tokenNorm = strtoupper(trim($token));
        foreach ($this->titlePatterns as $pattern) {
            if ($tokenNorm === strtoupper($pattern)) {
                return true;
            }
        }
        return false;
    }

    protected function extractKennelPrefix(array $tokens): array
    {
        if (empty($tokens)) {
            return [$tokens, []];
        }

        $lowerTokens = array_map('mb_strtolower', $tokens);

        foreach (KennelRules::complex() as $pattern) {
            $parts = explode(' ', $pattern);
            $len   = count($parts);

            if (array_slice($lowerTokens, 0, $len) === $parts) {
                return [
                    array_slice($tokens, $len),
                    array_slice($tokens, 0, $len),
                ];
            }
        }

        $prefixTokens = [];
        $remaining = $tokens;

        while (!empty($remaining)) {
            $first = mb_strtolower($remaining[0]);

            if (in_array($first, $this->prefixWords, true)) {
                $prefixTokens[] = array_shift($remaining);
            } else {
                if (!empty($prefixTokens) && !empty($remaining)) {
                    $prefixTokens[] = array_shift($remaining);
                }
                break;
            }
        }

        return [$remaining, $prefixTokens];
    }

    protected function extractKennelSuffix(array $tokens): array
    {
        if (empty($tokens)) {
            return [$tokens, []];
        }

        $lower = array_map(fn ($t) => mb_strtolower($t), $tokens);
        $joined = implode(' ', $lower);

        foreach (KennelRules::complex() as $pattern) {
            if (str_contains($joined, $pattern) && !in_array($pattern, $this->suffixPatterns, true)) {
                $this->suffixPatterns[] = $pattern;
            }
        }

        $matchedSuffix = null;
        foreach ($this->suffixPatterns as $pattern) {
            if (str_contains($joined, $pattern)) {
                $matchedSuffix = $pattern;
                break;
            }
        }

        if (!$matchedSuffix) {
            return [$tokens, []];
        }

        $suffixParts = explode(' ', $matchedSuffix);
        $suffixLen   = count($suffixParts);

        for ($i = count($tokens) - $suffixLen; $i >= 0; $i--) {
            $slice = array_slice($lower, $i, $suffixLen);
            if ($slice === $suffixParts) {
                $kennelSuffixTokens = array_slice($tokens, $i, $suffixLen);
                $remaining = $tokens;
                array_splice($remaining, $i, $suffixLen);
                return [$remaining, $kennelSuffixTokens];
            }
        }

        return [$tokens, []];
    }

    protected function buildNameFromTokens(array $tokens): string
    {
        if (empty($tokens)) {
            return '';
        }
        return $this->normalizeNameFromTokens($tokens);
    }

    protected function normalizeNameFromTokens(array $tokens): string
    {
        if (empty($tokens)) {
            return '';
        }

        $normalized = array_map(function ($t) {
            $t = mb_strtolower($t);
            return mb_strtoupper(mb_substr($t, 0, 1)) . mb_substr($t, 1);
        }, $tokens);

        return implode(' ', $normalized);
    }

    protected function extractRegNo(string $text): ?string
    {
        foreach (RegNoRules::patterns() as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }
        return null;
    }
}