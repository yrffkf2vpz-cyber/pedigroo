<?php

namespace App\Services\WebImporter\Extractors;

use App\Services\WebImporter\Contracts\PedigreeExtractorInterface;
use App\Services\WebImporter\DTO\DogDto;

class GenericPedigreeExtractor implements PedigreeExtractorInterface
{
    public function extract(string $html, DogDto $rootDog): DogDto
    {
        $lines = $this->extractPedigreeLines($html);
        if (empty($lines)) {
            return $rootDog;
        }

        // 1. generáció (root + sire + dam)
        if (isset($lines[0])) {
            $this->fillFirstGeneration($rootDog, $lines[0]);
        }

        // 2. generáció (nagyszülok)
        if (isset($lines[1])) {
            $this->fillSecondGeneration($rootDog, $lines[1]);
        }

        // 3. generáció (dédszülok)
        if (isset($lines[2])) {
            $this->fillThirdGeneration($rootDog, $lines[2]);
        }

        return $rootDog;
    }

    private function extractPedigreeLines(string $html): array
    {
        $html = strip_tags($html);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        $html = preg_replace('/\s+/', ' ', $html);

        $patterns = [
            '/Stamtavla(.*?)Sisarukset/isu',
            '/Stamtavla(.*)$/isu',
            '/Sukutaulu(.*?)Sisarukset/isu',
            '/Pedigree(.*?)Offspring/isu',
            '/Pedigree(.*)$/isu',
        ];

        $block = null;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $block = trim($m[1]);
                break;
            }
        }

        if (!$block) {
            return [];
        }

        $rows = preg_split('/\s{2,}/u', $block);
        $rows = array_filter(array_map('trim', $rows));

        $lines = [];
        $current = [];

        foreach ($rows as $row) {
            if ($this->containsRegNo($row)) {
                $current[] = $row;
            } else {
                if (!empty($current)) {
                    $lines[] = $current;
                    $current = [];
                }
            }
        }

        if (!empty($current)) {
            $lines[] = $current;
        }

        return $lines;
    }

    private function fillFirstGeneration(DogDto $root, array $line): void
    {
        if (isset($line[1])) {
            $root->sire = $this->makeDogFromLine($line[1]);
        }

        if (isset($line[2])) {
            $root->dam = $this->makeDogFromLine($line[2]);
        }
    }

    private function fillSecondGeneration(DogDto $root, array $line): void
    {
        if ($root->sire) {
            if (isset($line[1])) {
                $root->sire->sire = $this->makeDogFromLine($line[1]);
            }
            if (isset($line[2])) {
                $root->sire->dam = $this->makeDogFromLine($line[2]);
            }
        }

        if ($root->dam) {
            if (isset($line[3])) {
                $root->dam->sire = $this->makeDogFromLine($line[3]);
            }
            if (isset($line[4])) {
                $root->dam->dam = $this->makeDogFromLine($line[4]);
            }
        }
    }

    private function fillThirdGeneration(DogDto $root, array $line): void
    {
        $positions = [
            [$root->sire->sire ?? null, 1, 2],
            [$root->sire->dam ?? null, 3, 4],
            [$root->dam->sire ?? null, 5, 6],
            [$root->dam->dam ?? null, 7, 8],
        ];

        foreach ($positions as [$node, $idx1, $idx2]) {
            if ($node) {
                if (isset($line[$idx1])) {
                    $node->sire = $this->makeDogFromLine($line[$idx1]);
                }
                if (isset($line[$idx2])) {
                    $node->dam = $this->makeDogFromLine($line[$idx2]);
                }
            }
        }
    }

    private function makeDogFromLine(string $line): DogDto
    {
        $dto = new DogDto();

        $dto->regNo = $this->extractRegNo($line);

        if ($dto->regNo) {
            $dto->name = trim(str_replace($dto->regNo, '', $line));
        } else {
            $dto->name = trim($line);
        }

        return $dto;
    }

    private function extractRegNo(string $text): ?string
    {
        $patterns = [
            '/MET\.[A-Za-z]{2}\.[0-9\/]+/u',
            '/MET\s+[A-Z]{2}\s+[0-9\/]+/u',
            '/FIN[0-9\/]{3,}/u',
            '/CKC[A-Z0-9]{5,}/u',
            '/AKCSB[A-Z][0-9]{3,}/u',
            '/VDH[-A-Z0-9\/]{3,}/u',
            '/KUZ[0-9\/]{3,}/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return trim($m[0]);
            }
        }

        return null;
    }

    private function containsRegNo(string $text): bool
    {
        return (bool) $this->extractRegNo($text);
    }
}