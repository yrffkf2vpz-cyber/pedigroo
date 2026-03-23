<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class OwnerService
{
    /**
     * Aktivál egy tulajdonost:
     * - pedroo_owners → owners
     *
     * @param string|null $name
     * @return int|null owner_id
     */
    public function activateOwner(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        // 1) Publikus owners táblában van?
        $existing = DB::table('owners')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Pedroo owners táblában van?
        $po = DB::table('pedroo_owners')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($po) {
            $id = DB::table('owners')->insertGetId([
                'name'       => $po->name,
                'country'    => $po->country,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pedroo_owners')->where('id', $po->id)->delete();

            return $id;
        }

        // 3) Ha sehol nincs → új owner létrehozása
        return DB::table('owners')->insertGetId([
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}