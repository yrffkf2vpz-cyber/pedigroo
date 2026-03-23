<?php

namespace App\Services\Pedroo;

use App\Services\Normalizers\DogNormalizer;
use App\Services\Normalizers\AdvancedNameParser;
use Illuminate\Support\Facades\DB;
use App\Pedroo\Intelligence\Color\ColorNormalizer;

class DogActivationService
{
    /**
     * Aktivál egy kutyát a sandboxból a végleges dogs táblába.
     */
    public function activateDog(array $row): int
    {
        $name        = $row['name']        ?? null;
        $breed       = $row['breed']       ?? null;
        $regNo       = $row['reg_no']      ?? null;
        $sex         = $row['sex']         ?? null;
        $birthDate   = $row['birth_date']  ?? null;
        $country     = $row['country']     ?? null;
        $ownerName   = $row['owner_name']  ?? null;
        $breederName = $row['breeder_name']?? null;
        $rawColor    = $row['color']       ?? null;

        // 1) Normalizálás → pedroo_dogs.id
        $pedrooDogId = DogNormalizer::id($name, $breed, $regNo);

        // 2) Parser → kennel + hívónév
        $parser = new AdvancedNameParser();
        $parsed = $parser->parse($name);

        $kennelName = $parsed['kennel_name'] ?? null;

        // 3) Kennel aktiválása
        $kennelId = null;
        if ($kennelName) {
            $kennelService = new KennelService();
            $kennelId = $kennelService->activateKennel($kennelName, $pedrooDogId);
        }

        // 4) SZÍN NORMALIZÁLÁS
        $colorId = null;
        if ($rawColor && $breed) {
            $normalizer = app(ColorNormalizer::class);

            $normalized = $normalizer->normalize(
                $breed,     // breed_id vagy breed_code – nálad a $breed mező tartalma
                $rawColor
            );

            if ($normalized) {
                $colorId = $normalized->id;
            }
        }

        // 5) Végleges dogs rekord
        $dogId = DB::table('dogs')->insertGetId([
            'pedroo_dog_id' => $pedrooDogId,
            'name'          => $parsed['call_name'] ?: $name,
            'sex'           => $sex,
            'birth_date'    => $birthDate,
            'country'       => $country,
            'kennel_id'     => $kennelId,
            'owner_name'    => $ownerName,
            'breeder_name'  => $breederName,
            'color_id'      => $colorId,   // ← itt tároljuk a normalizált színt
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return $dogId;
    }
}
