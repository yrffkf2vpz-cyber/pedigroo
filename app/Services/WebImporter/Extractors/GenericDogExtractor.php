<?php

namespace App\Services\WebImporter\Extractors;

use App\Services\WebImporter\Contracts\DogExtractorInterface;
use App\Services\WebImporter\DTO\DogDto;

class GenericDogExtractor implements DogExtractorInterface
{
    public function extract(string $html): DogDto
    {
        $dog = new DogDto();

        $dog->name   = $this->extractName($html);
        $dog->regNo  = $this->extractRegNo($html);
        $dog->breed  = $this->extractBreed($html);
        $dog->dob    = $this->extractDob($html);
        $dog->sex    = $this->extractSex($html);
        $dog->chip   = $this->extractChip($html);
        $dog->kennel = $this->extractKennel($html);
        $dog->breeder= $this->extractBreeder($html);
        $dog->owner  = $this->extractOwner($html);

        $dog->sire   = $this->extractParent($html, ['sire', 'far', 'father', 'isä', 'apa', 'kan']);
        $dog->dam    = $this->extractParent($html, ['dam', 'mor', 'mother', 'emä', 'anya', 'szuka']);

        // egészség, kiállítások, almok késobb külön metódusokkal
        return $dog;
    }

    private function extractName(string $html): ?string
    {
        // kuvaszadatbazis.hu: a lap tetején a név + reg_no, de a név önmagában is szerepel
        // más oldalakon: "Hund:", "Dog:", stb.

        // 1) próbáljuk meg a "Hund:" / "Dog:" / "Kutya:" mintát
        if (preg_match('/(?:Hund|Dog|Kutya)\s*:\s*([^\r\n<]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // 2) kuvaszadatbazis.hu: pl. "Megyeri-Nomád Császár, MET.Ku.9141/12"
        if (preg_match('/^([^\r\n,]+),\s*(MET\.[A-Za-z]{2}\.[0-9\/]+)/mu', $html, $m)) {
            return trim($m[1]);
        }

        // 3) más oldalak: "male Csipkéskúti Nyitány" ? vegyük az elso nagybetus szekvenciát a "male/female" után
        if (preg_match('/\b(male|female)\s+([A-ZÁÉÍÓÖOÚÜU][^\r\n]+)/iu', $html, $m)) {
            return trim($m[2]);
        }

        // 4) fallback: elso sor, ami tartalmaz szóközt és nagybetut
        if (preg_match('/^([A-ZÁÉÍÓÖOÚÜU][A-Za-z0-9ÁÉÍÓÖOÚÜUáéíóöoúüu\-\s"]{3,})$/mu', $html, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function extractRegNo(string $html): ?string
    {
        // többféle minta: MET.Ku.9141/12, MET MV 7563/24, FIN47665/03, CKCSTB1011416, AKCSBWF103826, stb.

        $patterns = [
            '/MET\.[A-Za-z]{2}\.[0-9\/]+/u',      // MET.Ku.9141/12
            '/MET\s+[A-Z]{2}\s+[0-9\/]+/u',       // MET MV 7563/24
            '/FIN[0-9\/]{3,}/u',                  // FIN47665/03
            '/CKC[A-Z0-9]{5,}/u',                 // CKCSTB1011416
            '/AKCSB[A-Z][0-9]{3,}/u',             // AKCSBWF103826, AKCSBWE841372
            '/VDH[-A-Z0-9\/]{3,}/u',              // VDH-KVD494 stb.
            '/KUZ[0-9\/]{3,}/u',                  // KUZ10392/89
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                return trim($m[0]);
            }
        }

        // ha semmi, akkor null
        return null;
    }

    private function extractBreed(string $html): ?string
    {
        // "Breed:", "Rotu", "Ras", "Fajta", stb.

        if (preg_match('/(?:Breed|Rotu|Ras|Fajta)\s*:\s*([^\r\n<]+)/iu', $html, $m)) {
            return trim($m[1]);
        }

        // kuvaszadatbazis.hu: a fajta sokszor implicit (Kuvasz), de ha látjuk:
        if (stripos($html, 'Kuvasz') !== false) {
            return 'Kuvasz';
        }

        return null;
    }

    private function extractDob(string $html): ?string
    {
        // többféle nyelv: "Date of Birth:", "Születés", "Születési dátum", "Födelsedatum", stb.

        // 1) angol: "Date of Birth:"
        if (preg_match('/Date of Birth\s*:\s*([^\r\n<]+)/iu', $html, $m)) {
            return \App\Services\WebImporter\Support\HungarianDateParser::parse(trim($m[1]));
        }

        // 2) magyar: "Születés / Kennel 20 Július 2012"
        if (preg_match('/Születés\s*\/\s*Kennel\s*([0-9\.A-Za-zÁÉÍÓÖOÚÜUáéíóöoúüu\s]+)/u', $html, $m)) {
            return \App\Services\WebImporter\Support\HungarianDateParser::parse(trim($m[1]));
        }

        // 3) finn: "s. 29.9.2003"
        if (preg_match('/s\.\s*([0-9\.]{6,})/u', $html, $m)) {
            return \App\Services\WebImporter\Support\HungarianDateParser::parse(trim($m[1]));
        }

        return null;
    }

    private function extractSex(string $html): ?string
    {
        // magyar: "Neme    Kan" / "Neme    Szuka"
        if (preg_match('/Neme\s*([A-Za-zÁÉÍÓÖOÚÜUáéíóöoúüu]+)/u', $html, $m)) {
            $v = mb_strtolower(trim($m[1]), 'UTF-8');
            if (in_array($v, ['kan', 'male'])) {
                return 'male';
            }
            if (in_array($v, ['szuka', 'female', 'narttu'])) {
                return 'female';
            }
        }

        // angol: "male Csipkéskúti Nyitány"
        if (preg_match('/\b(male|female)\b/iu', $html, $m)) {
            $v = strtolower($m[1]);
            return $v === 'male' ? 'male' : 'female';
        }

        // finn: "uros" / "narttu"
        if (preg_match('/\b(uros|narttu)\b/iu', $html, $m)) {
            $v = mb_strtolower($m[1], 'UTF-8');
            if ($v === 'uros') {
                return 'male';
            }
            if ($v === 'narttu') {
                return 'female';
            }
        }

        return null;
    }

    private function extractChip(string $html): ?string
    {
        // magyar: "Microchip száma  900182000184114"
        if (preg_match('/Microchip\s+száma\s*([0-9]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // más: "Chip-nr:", "Chip number:"
        if (preg_match('/Chip[-\s]?nr\s*:\s*([0-9]+)/iu', $html, $m)) {
            return trim($m[1]);
        }

        // finn: "Tunnistusmerkintä"
        if (preg_match('/Tunnistusmerkintä\s*([0-9]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function extractKennel(string $html): ?string
    {
        // magyar: "Születés / Kennel 20 Július 2012  Megyeri-Nomád Kennel"
        if (preg_match('/Születés\s*\/\s*Kennel\s*[0-9\.A-Za-zÁÉÍÓÖOÚÜUáéíóöoúüu\s]+?\s+([A-ZÁÉÍÓÖOÚÜU][^|\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // "Tenyészto / Kennel  Baráth Zsolt  Megyeri-Nomád Kennel"
        if (preg_match('/Tenyészto\s*\/\s*Kennel\s*[^\r\n]*?\s+([A-ZÁÉÍÓÖOÚÜU][^|\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // más oldalakon: "Kennel", "Csipkéskúti", stb. – ezt késobb finomíthatjuk
        return null;
    }

    private function extractBreeder(string $html): ?string
    {
        // magyar: "Tenyészto / Kennel  Baráth Zsolt  Megyeri-Nomád Kennel"
        if (preg_match('/Tenyészto\s*\/\s*Kennel\s*([A-ZÁÉÍÓÖOÚÜU][^|\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // angol: "Breeders  Hungary  Hedvig Balázs  Csipkéskúti"
        if (preg_match('/Breeders[^\r\n]*?\s+([A-ZÁÉÍÓÖOÚÜU][^\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // finn: "Kasvattaja"
        if (preg_match('/Kasvattaja\s*([A-ZÁÉÍÓÖOÚÜU][^\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function extractOwner(string $html): ?string
    {
        // magyar: "Tulajdonos / Kennel  Kiss M. Sándor  Kardosparti Kennel"
        if (preg_match('/Tulajdonos\s*\/\s*Kennel\s*([A-ZÁÉÍÓÖOÚÜU][^|\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // angol: "Owners  Hungary  Tamás Franczva Primary Owner"
        if (preg_match('/Owners[^\r\n]*?\s+([A-ZÁÉÍÓÖOÚÜU][^\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        // finn: "Omistaja"
        if (preg_match('/Omistaja[^\r\n]*?\s+([A-ZÁÉÍÓÖOÚÜU][^\r\n]+)/u', $html, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    /**
     * @param string   $html
     * @param string[] $keywords
     */
    private function extractParent(string $html, array $keywords): ?DogDto
    {
        $lower = mb_strtolower($html, 'UTF-8');

        foreach ($keywords as $keyword) {
            $kw = mb_strtolower($keyword, 'UTF-8');

            // egyszeru minta: "Sire: Csipkéskúti Czókmók"
            if (preg_match('/' . preg_quote($keyword, '/') . '\s*:\s*([^\r\n<]+)/iu', $html, $m)) {
                $line = trim($m[1]);
                $dto  = new DogDto();

                // próbáljuk reg_no + name bontást
                $dto->regNo = $this->extractRegNo($line);
                if ($dto->regNo) {
                    $dto->name = trim(str_replace($dto->regNo, '', $line));
                } else {
                    $dto->name = $line;
                }

                return $dto;
            }

            // kuvaszadatbazis.hu: "Kan Szerkesztés (I13) Borza-Parti "Orség" Juhar, MET.Ku.7968/07,   sz. 04 Május 2007, ..."
            if (in_array($kw, ['apa', 'kan', 'sire', 'father'], true)) {
                if (preg_match('/Kan\s+Szerkesztés\s*\(I[0-9]+\)\s*([^\r\n,]+),\s*(MET\.[A-Za-z]{2}\.[0-9\/]+)/u', $html, $m)) {
                    $dto = new DogDto();
                    $dto->name  = trim($m[1]);
                    $dto->regNo = trim($m[2]);
                    return $dto;
                }
            }

            if (in_array($kw, ['anya', 'szuka', 'dam', 'mother'], true)) {
                if (preg_match('/Szuka\s+Szerkesztés\s*\(I[0-9]+\)\s*([^\r\n,]+),\s*(MET\.[A-Za-z]{2}\.[0-9\/]+)/u', $html, $m)) {
                    $dto = new DogDto();
                    $dto->name  = trim($m[1]);
                    $dto->regNo = trim($m[2]);
                    return $dto;
                }
            }
        }

        return null;
    }
}