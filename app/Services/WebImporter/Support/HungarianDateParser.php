<?php

namespace App\Services\WebImporter\Support;

class HungarianDateParser
{
    protected static array $monthsHu = [
        'január'   => '01',
        'február'  => '02',
        'március'  => '03',
        'április'  => '04',
        'május'    => '05',
        'június'   => '06',
        'július'   => '07',
        'augusztus'=> '08',
        'szeptember'=> '09',
        'október'  => '10',
        'november' => '11',
        'december' => '12',
    ];

    public static function parse(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $raw = trim($raw);

        // 1) "20 Július 2012" típusú (kuvaszadatbazis.hu)
        if (preg_match('/^(\d{1,2})\s+([A-Za-zÁÉÍÓÖOÚÜUáéíóöoúüu]+)\s+(\d{4})$/u', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = mb_strtolower($m[2], 'UTF-8');
            $year  = $m[3];

            $monthNum = self::$monthsHu[$month] ?? null;
            if ($monthNum) {
                return sprintf('%s-%s-%s', $year, $monthNum, $day);
            }
        }

        // 2) "07 May 2024" típusú (angol)
        if (preg_match('/^(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})$/u', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = strtolower($m[2]);
            $year  = $m[3];

            $monthsEn = [
                'january' => '01', 'february' => '02', 'march' => '03',
                'april' => '04', 'may' => '05', 'june' => '06',
                'july' => '07', 'august' => '08', 'september' => '09',
                'october' => '10', 'november' => '11', 'december' => '12',
            ];

            $monthNum = $monthsEn[$month] ?? null;
            if ($monthNum) {
                return sprintf('%s-%s-%s', $year, $monthNum, $day);
            }
        }

        // 3) "29.9.2003" típusú (finn)
        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $year  = $m[3];

            return sprintf('%s-%s-%s', $year, $month, $day);
        }

        // ha semmi nem illik, visszaadjuk nyersen (vagy null-t, ha szigorú akarsz lenni)
        return null;
    }
}