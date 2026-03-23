<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class KennelService
{
    public function activateKennel(?string $kennelName, ?int $dogId = null): ?int
    {
        if (!$kennelName) {
            return null;
        }

        $existing = DB::table('kennels')
            ->whereRaw('LOWER(name) = ?', [strtolower($kennelName)])
            ->first();

        if ($existing) {
            if ($dogId) {
                $this->attachDogToKennel($existing->id, $dogId);
            }
            return $existing->id;
        }

        $pk = DB::table('pedroo_kennels')
            ->whereRaw('LOWER(name) = ?', [strtolower($kennelName)])
            ->first();

        if ($pk) {
            $breederId = $this->activateBreeder($pk->breeder_name);
            $ownerId   = $this->activateOwner($pk->owner_name);

            $kennelId = DB::table('kennels')->insertGetId([
                'name'       => $pk->name,
                'country'    => $pk->country,
                'breeder_id' => $breederId,
                'owner_id'   => $ownerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pedroo_kennels')->where('id', $pk->id)->delete();

            if ($dogId) {
                $this->attachDogToKennel($kennelId, $dogId);
            }

            return $kennelId;
        }

        $kennelId = DB::table('kennels')->insertGetId([
            'name'       => $kennelName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($dogId) {
            $this->attachDogToKennel($kennelId, $dogId);
        }

        return $kennelId;
    }

    protected function attachDogToKennel(int $kennelId, int $dogId): void
    {
        DB::table('kennel_dogs')->updateOrInsert(
            [
                'kennel_id' => $kennelId,
                'dog_id'    => $dogId,
            ],
            [
                'updated_at' => now(),
            ]
        );
    }

    protected function activateBreeder(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        $existing = DB::table('breeders')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($existing) {
            return $existing->id;
        }

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

        return DB::table('breeders')->insertGetId([
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function activateOwner(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        $existing = DB::table('owners')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($existing) {
            return $existing->id;
        }

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

        return DB::table('owners')->insertGetId([
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}