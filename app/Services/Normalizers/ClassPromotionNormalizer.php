<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class ClassPromotionNormalizer
{
    public static function promote(?string $raw): ?int
    {
        if (!$raw) {
            return null;
        }

        $clean = self::clean($raw);
        $code  = self::mapToCode($clean);

        // 1) Check if class already exists
        $existing = DB::table('pd_classes')
            ->where('code', $code)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Insert new class
        return DB::table('pd_classes')->insertGetId([
            'code'       => $code,
            'label'      => ucfirst(strtolower(str_replace('_', ' ', $code))),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /* ---------------------------------------------------------
     *  CLEANER — lowercase, remove accents, keep letters/spaces
     * --------------------------------------------------------- */
    protected static function clean(string $value): string
    {
        $value = strtolower(trim($value));

        // remove accents (Hungarian + Western European)
        $value = strtr($value, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a',
            'ç'=>'c',
            'è'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','î'=>'i','ï'=>'i',
            'ñ'=>'n',
            'ò'=>'o','ô'=>'o','õ'=>'o','ø'=>'o',
            'ù'=>'u','û'=>'u','ü'=>'u',
            'ý'=>'y','ÿ'=>'y'
        ]);

        // keep only letters and spaces
        $value = preg_replace('/[^a-z ]/', '', $value);

        return trim($value);
    }

    /* ---------------------------------------------------------
     *  MULTILINGUAL MAPPING — FCI global class system
     * --------------------------------------------------------- */
    protected static function mapToCode(string $clean): string
    {
        $map = [

            // BABY (3–6 months)
            'baby'                  => 'BABY',
            'minor puppy'           => 'BABY',
            'bebi'                  => 'BABY',
            'bebi osztaly'          => 'BABY',
            'babyklasse'            => 'BABY',
            'classe baby'           => 'BABY',
            'cachorros'             => 'BABY',

            // PUPPY (6–9 months)
            'puppy'                 => 'PUPPY',
            'kolyok'                => 'PUPPY',
            'kolyok osztaly'        => 'PUPPY',
            'jungstenklasse'        => 'PUPPY',
            'classe puppy'          => 'PUPPY',

            // JUNIOR (9–18 months)
            'junior'                => 'JUNIOR',
            'youth'                 => 'JUNIOR',
            'fiatal'                => 'JUNIOR',
            'fiatal osztaly'        => 'JUNIOR',
            'jugendklasse'          => 'JUNIOR',
            'classe jeune'          => 'JUNIOR',
            'giovani'               => 'JUNIOR',
            'jovenes'               => 'JUNIOR',

            // INTERMEDIATE (15–24 months)
            'intermediate'          => 'INTERMEDIATE',
            'inter'                 => 'INTERMEDIATE',
            'novendek'              => 'INTERMEDIATE',
            'novendek osztaly'      => 'INTERMEDIATE',
            'zwischenklasse'        => 'INTERMEDIATE',
            'classe intermediaire'  => 'INTERMEDIATE',
            'intermedia'            => 'INTERMEDIATE',

            // OPEN (15+ months)
            'open'                  => 'OPEN',
            'nyilt'                 => 'OPEN',
            'nyilt osztaly'         => 'OPEN',
            'offene klasse'         => 'OPEN',
            'classe ouverte'        => 'OPEN',
            'libera'                => 'OPEN',
            'abierta'               => 'OPEN',

            // WORKING (15+ months, with working certificate)
            'working'               => 'WORKING',
            'munka'                 => 'WORKING',
            'munka osztaly'         => 'WORKING',
            'gebrauchshundklasse'   => 'WORKING',
            'classe travail'        => 'WORKING',
            'lavoro'                => 'WORKING',
            'trabajo'               => 'WORKING',

            // CHAMPION (15+ months)
            'champion'              => 'CHAMPION',
            'ch'                    => 'CHAMPION',
            'championklasse'        => 'CHAMPION',
            'classe champion'       => 'CHAMPION',
            'campioni'              => 'CHAMPION',
            'campeones'             => 'CHAMPION',

            // VETERAN (8+ years)
            'veteran'               => 'VETERAN',
            'veteran osztaly'       => 'VETERAN',
            'veteranok'             => 'VETERAN',
            'veteranenklasse'       => 'VETERAN',
            'classe veteran'        => 'VETERAN',
            'veterani'              => 'VETERAN',
            'veteranos'             => 'VETERAN',
        ];

        return $map[$clean] ?? strtoupper($clean);
    }
}