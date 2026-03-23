<?php

namespace App\Services\Parsers;

use Spatie\PdfToText\Pdf;

class GenericPdfParser
{
    public function parseFile(string $path): array
    {
        // 1) PDF → text
        $text = Pdf::getText($path);

        // 2) Sorokra bontás
        $lines = preg_split("/\r\n|\n|\r/", $text);

        // 3) Esemény metaadatok (egyelőre egyszerűsítve)
        $eventName = $this->guessEventName($lines);
        $date      = $this->guessDate($lines);
        $country   = "Hungary";
        $city      = "Budapest";
        $location  = "Unknown";
        $club      = "Unknown";

        // 4) Eredmények feldolgozása
        $results = $this->extractResults($lines);

        return [
            "event_name" => $eventName,
            "date"       => $date,
            "country"    => $country,
            "city"       => $city,
            "location"   => $location,
            "club"       => $club,
            "judges"     => [],
            "rings"      => [],
            "results"    => $results,
        ];
    }

    /* ---------------------------------------------------------
     *  EVENT METADATA
     * --------------------------------------------------------- */

    protected function guessEventName(array $lines): string
    {
        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '') continue;

            if (stripos($trim, 'CAC') !== false || stripos($trim, 'Show') !== false) {
                return $trim;
            }
        }

        return "Dog Show";
    }

    protected function guessDate(array $lines): string
    {
        foreach ($lines as $line) {
            if (preg_match('/\d{4}\.\d{2}\.\d{2}/', $line, $m)) {
                // 2025.03.21 → 2025-03-21
                return str_replace('.', '-', $m[0]);
            }
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $line, $m)) {
                return $m[0];
            }
        }

        return date('Y-m-d');
    }

    /* ---------------------------------------------------------
     *  RESULTS
     * --------------------------------------------------------- */

    protected function extractResults(array $lines): array
    {
        $results = [];

        foreach ($lines as $line) {
            $clean = trim($line);
            if ($clean === '') {
                continue;
            }

            // Minimális szűrés: legyen benne legalább egy szám (helyezés / reg_no / év)
            if (!preg_match('/\d/', $clean)) {
                continue;
            }

            // Itt most még nem táblázatot, hanem "raw_line" szintű eredményt gyűjtünk
            $regNo = $this->extractRegNo($clean);

            $results[] = [
                "dog_name"  => $this->extractDogName($clean),
                "breed"     => $this->extractBreed($clean),
                "class"     => $this->extractClass($clean),
                "placement" => $this->extractPlacement($clean),
                "title"     => $this->extractTitle($clean),
                "reg_no"    => $regNo,
                "raw_line"  => $clean,
            ];
        }

        return $results;
    }

    /* ---------------------------------------------------------
     *  FIELD HELPERS (egyelőre egyszerűk, később okosítjuk)
     * --------------------------------------------------------- */

    protected function extractBreed(string $line): string
    {
        // később: fajtalista alapján
        return "Unknown";
    }

    protected function extractClass(string $line): string
    {
        if (stripos($line, 'Open') !== false) return "Open";
        if (stripos($line, 'Junior') !== false) return "Junior";
        if (stripos($line, 'Puppy') !== false) return "Puppy";
        if (stripos($line, 'Veteran') !== false) return "Veteran";
        return "";
    }

    protected function extractPlacement(string $line): string
    {
        // nagyon egyszerű: első előforduló 1/2/3/4
        if (preg_match('/\b1\b/', $line)) return "1";
        if (preg_match('/\b2\b/', $line)) return "2";
        if (preg_match('/\b3\b/', $line)) return "3";
        if (preg_match('/\b4\b/', $line)) return "4";
        return "";
    }

    protected function extractDogName(string $line): string
    {
        // első verzió: a teljes sor, később: minták alapján vágjuk
        return $line;
    }

    protected function extractTitle(string $line): string
    {
        if (stripos($line, 'CACIB') !== false) return "CACIB";
        if (stripos($line, 'CAC') !== false) return "CAC";
        if (stripos($line, 'BOB') !== false) return "BOB";
        if (stripos($line, 'BOS') !== false) return "BOS";
        return "";
    }

    /* ---------------------------------------------------------
     *  REGISTRATION NUMBER EXTRACTION
     * --------------------------------------------------------- */

    protected function extractRegNo(string $text): ?string
    {
        // Mudi minták: 1408/B/96, 17/B, 1981/B/00, 322/B/89, 3978/R/10, 6948/R/13, 98/B
        $pattern = '/([0-9]{1,6}[A-Z]?(?:\/[A-Z])?\/[0-9]{1,4})/i';

        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}