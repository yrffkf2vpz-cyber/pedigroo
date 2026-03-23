<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class BreederService
{
    /**
     * Aktivál egy tenyésztőt:
     * - pedroo_breeders → breeders
     *
     * @param string|null $name
     * @return int|null breeder_id
     */
    public function activateBreeder(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        // 1) Publikus breeders táblában van?
        $existing = DB::table('breeders')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Pedroo breeders táblában van?
        $pb = DB::table('pedroo_breeders')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($pb) {
            $id = DB::table('breeders')->insertGetId([
                'name'       => $pb->name,
                'country'    => $pb->country,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pedroo_breeders')->where('id', $pb->id)->delete();

            return $id;
        }

        // 3) Ha sehol nincs → új breeder létrehozása
        return DB::table('breeders')->insertGetId([
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}