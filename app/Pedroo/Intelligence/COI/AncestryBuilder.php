<?php

namespace App\Pedroo\Intelligence\COI;

use App\Models\Dog;
use App\Pedroo\Intelligence\COI\Models\PdDogAncestry;

class AncestryBuilder
{
    /**
     * Teljes ancestry újraépítése egy kutyára
     */
    public function buildForDog(Dog $dog): void
    {
        PdDogAncestry::where('dog_id', $dog->id)->delete();

        if ($dog->sire_id) {
            $this->addAncestors($dog->id, $dog->sire_id, 1);
        }

        if ($dog->dam_id) {
            $this->addAncestors($dog->id, $dog->dam_id, 1);
        }
    }

    /**
     * Rekurzív ős-hozzáadás generációs mélységgel
     */
    protected function addAncestors(int $dogId, int $ancestorId, int $generation): void
    {
        PdDogAncestry::updateOrCreate(
            [
                'dog_id'      => $dogId,
                'ancestor_id' => $ancestorId,
            ],
            [
                'generations' => $generation,
            ]
        );

        $ancestor = Dog::find($ancestorId);

        if (!$ancestor) {
            return;
        }

        if ($ancestor->sire_id) {
            $this->addAncestors($dogId, $ancestor->sire_id, $generation + 1);
        }

        if ($ancestor->dam_id) {
            $this->addAncestors($dogId, $ancestor->dam_id, $generation + 1);
        }
    }
}