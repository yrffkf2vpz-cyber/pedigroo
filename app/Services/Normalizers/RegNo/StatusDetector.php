<?php

namespace App\Services\Normalizers\RegNo;

class StatusDetector
{
    protected array $map = [
        'B' => 'open_studbook',
        'H' => 'imported',
        // később: T, O, A, F, ...
    ];

    public function detect(string $raw): ?array
    {
        if (preg_match('/\/([A-Z])\//', $raw, $m)) {
            $code = $m[1];

            if (isset($this->map[$code])) {
                return [
                    'code'    => $code,
                    'meaning' => $this->map[$code],
                ];
            }
        }

        return null;
    }
}