<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class MkszHtmlParser
{
    public function parseFile(string $path): array
    {
        $html = file_get_contents($path);
        $crawler = new Crawler($html);

        return [
            "event_name" => $this->extractEventName($crawler),
            "date"       => $this->extractDate($crawler),
            "country"    => "Hungary",
            "city"       => $this->extractCity($crawler),
            "location"   => $this->extractLocation($crawler),
            "club"       => "MKSZ",
            "judges"     => $this->extractJudges($crawler),
            "rings"      => $this->extractRings($crawler),
            "results"    => $this->extractResults($crawler),
        ];
    }

    /* ---------------------------------------------------------
     *  EVENT METADATA
     * --------------------------------------------------------- */

    protected function extractEventName(Crawler $crawler): string
    {
        // Tipikus MKSZ HTML: <h1>CACIB Budapest</h1>
        return trim($crawler->filter('h1')->first()->text('MKSZ Show'));
    }

    protected function extractDate(Crawler $crawler): string
    {
        // Később: automatikus dátumfelismerés
        return date('Y-m-d');
    }

    protected function extractCity(Crawler $crawler): string
    {
        return "Budapest";
    }

    protected function extractLocation(Crawler $crawler): string
    {
        return "Hungexpo";
    }

    /* ---------------------------------------------------------
     *  JUDGES
     * --------------------------------------------------------- */

    protected function extractJudges(Crawler $crawler): array
    {
        // Később: táblázatból
        return [
            ["name" => "Dr. Kovács János", "role" => "allround"],
            ["name" => "Maria Rossi", "role" => "group 1"],
        ];
    }

    /* ---------------------------------------------------------
     *  RINGS
     * --------------------------------------------------------- */

    protected function extractRings(Crawler $crawler): array
    {
        return [
            ["name" => "Ring 1", "number" => "1"],
            ["name" => "Ring 2", "number" => "2"],
        ];
    }

    /* ---------------------------------------------------------
     *  RESULTS
     * --------------------------------------------------------- */

    protected function extractResults(Crawler $crawler): array
{
    $results = [];

    $crawler->filter('table.results tr')->each(function (Crawler $row) use (&$results) {

        $cols = $row->filter('td');
        if ($cols->count() < 4) {
            return;
        }

        $breed     = trim($cols->eq(0)->text());
        $class     = trim($cols->eq(1)->text());
        $dogName   = trim($cols->eq(2)->text());
        $placement = trim($cols->eq(3)->text());
        $title     = $cols->count() > 4 ? trim($cols->eq(4)->text()) : "";
        $rawLine   = $row->text();

        // Regisztrációs szám felismerése
        $regNo = $this->extractRegNo($dogName)
              ?? $this->extractRegNo($rawLine);

        // DogNormalizer → pedroo_dogs
        $dogId = \App\Services\Normalizers\DogNormalizer::id(
            $dogName,
            $breed,
            $regNo
        );

        $results[] = [
            "dog_name"  => $dogName,
            "breed"     => $breed,
            "class"     => $class,
            "placement" => $placement,
            "title"     => $title,
            "reg_no"    => $regNo,
            "dog_id"    => $dogId,
            "raw_line"  => $rawLine,
        ];
    });

    return $results;
}

    /* ---------------------------------------------------------
     *  REGISTRATION NUMBER EXTRACTION
     * --------------------------------------------------------- */

    protected function extractRegNo(string $text): ?string
    {
        // Mudi példák: 1408/B/96, 17/B, 1981/B/00, 322/B/89, 3978/R/10 stb.
        $pattern = '/([0-9]{1,6}[A-Z]?(?:\/[A-Z])?\/[0-9]{1,4})/i';

        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}