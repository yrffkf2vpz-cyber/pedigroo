<?php

namespace App\Pedroo\Intelligence\Color;

use Illuminate\Support\Facades\DB;

class ColorNormalizer
{
    public function normalize(int $breedId, string $input)
    {
        $cleanInput = $this->clean($input);

        // fajtaspecifikus színek lekérése
        $colors = DB::table('pd_breed_colors')
            ->where('breed_id', $breedId)
            ->get(['id', 'color_name']);

        $best = null;
        $bestScore = -1;

        foreach ($colors as $color) {
            $cleanColor = $this->clean($color->color_name);

            // fuzzy hasonlítás
            similar_text($cleanInput, $cleanColor, $percent);

            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best = $color;
            }
        }

        // ha nincs elég jó találat ? OTHER
        if ($bestScore < 40) {
            return DB::table('pd_breed_colors')
                ->where('breed_id', $breedId)
                ->where('color_name', 'Other color')
                ->first();
        }

        return $best;
    }

    private function clean(string $value): string
    {
        $value = strtolower($value);
        $value = str_replace(
            ['-', '/', '\\', '&', ',', '(', ')'],
            ' ',
            $value
        );
        $value = preg_replace('/\s+/', ' ', $value);
        return trim($value);
    }
}
