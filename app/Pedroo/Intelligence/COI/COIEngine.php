<?php

namespace App\Pedroo\Intelligence\COI;

use App\Models\Dog;
use App\Pedroo\Intelligence\COI\Models\PdDogAncestry;
use App\Pedroo\Intelligence\COI\Models\PdDogCoi;
use Illuminate\Support\Facades\DB;

class COIEngine
{
    public function __construct(
        protected AncestryBuilder $ancestryBuilder,
        protected COICalculator $calculator,
    ) {}

    /**
     * Egy kutya ancestry + COI újraszámolása
     */
    public function rebuildForDog(Dog $dog): void
    {
        DB::transaction(function () use ($dog) {
            $this->ancestryBuilder->buildForDog($dog);

            $coi = $this->calculator->calculateForDog($dog->id);

            PdDogCoi::updateOrCreate(
                ['dog_id' => $dog->id],
                [
                    'coi'           => $coi,
                    'calculated_at' => now(),
                ]
            );
        });
    }

    /**
     * Egy ős módosulása / bekerülése után
     * minden leszármazott COI-jának újraszámolása
     */
    public function rebuildForAllDescendants(Dog $ancestor): void
    {
        $descendantIds = PdDogAncestry::where('ancestor_id', $ancestor->id)
            ->pluck('dog_id')
            ->unique();

        foreach ($descendantIds as $dogId) {
            $dog = Dog::find($dogId);
            if ($dog) {
                $this->rebuildForDog($dog);
            }
        }
    }

    /**
     * COI lekérése (ha nincs, kiszámolja)
     */
    public function getCoiForDog(Dog $dog): float
    {
        $record = PdDogCoi::where('dog_id', $dog->id)->first();

        if ($record) {
            return (float) $record->coi;
        }

        $this->rebuildForDog($dog);

        return (float) PdDogCoi::where('dog_id', $dog->id)->value('coi') ?? 0.0;
    }


    /**
     * PÁROSÍTÁS COI SZÁMÍTÁSA
     * (sire × dam közös ősei alapján)
     */
    public function calculatePairCoi(Dog $sire, Dog $dam): float
    {
        // ancestry biztosan legyen friss
        $this->rebuildForDog($sire);
        $this->rebuildForDog($dam);

        $sireAnc = PdDogAncestry::where('dog_id', $sire->id)->get();
        $damAnc  = PdDogAncestry::where('dog_id', $dam->id)->get();

        // közös ősök
        $commonAncestors = $sireAnc->whereIn('ancestor_id', $damAnc->pluck('ancestor_id'));

        $coi = 0.0;

        foreach ($commonAncestors as $ancestor) {
            $ancestorId = $ancestor->ancestor_id;

            $n1 = $ancestor->generations;
            $n2 = $damAnc->firstWhere('ancestor_id', $ancestorId)->generations;

            // ős COI-ja (FA)
            $FA = (float) (PdDogCoi::where('dog_id', $ancestorId)->value('coi') ?? 0.0) / 100.0;

            // Wright formula
            $coi += pow(0.5, $n1 + $n2 + 1) * (1 + $FA);
        }

        return round($coi * 100, 4);
    }
}