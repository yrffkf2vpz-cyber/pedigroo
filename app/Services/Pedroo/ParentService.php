<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class ParentService
{
    /**
     * Aktiválja a kutya szülői kapcsolatokat:
     * - pedroo_parents → parents
     * - pedroo_children → children
     * - pedroo_families → families
     *
     * @param int $dogId (publikus dogs.id)
     * @param string $dogName (pedroo_dogs.real_name)
     */
    public function activateParents(int $dogId, string $dogName): void
    {
        // 1) Szülők aktiválása
        $this->activateParentLinks($dogId, $dogName);

        // 2) Gyerekek aktiválása
        $this->activateChildrenLinks($dogId, $dogName);

        // 3) Családfa aktiválása
        $this->activateFamilyLinks($dogId, $dogName);
    }

    /**
     * Szülői kapcsolatok átemelése
     */
    protected function activateParentLinks(int $dogId, string $dogName): void
    {
        $parents = DB::table('pedroo_parents')
            ->where('child_name', $dogName)
            ->get();

        foreach ($parents as $p) {
            // Szülő kutya ID lekérése vagy létrehozása
            $parentDogId = $this->findOrCreateDogByName($p->parent_name);

            DB::table('parents')->insert([
                'dog_id'       => $dogId,
                'parent_id'    => $parentDogId,
                'relation'     => $p->relation, // sire / dam
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            DB::table('pedroo_parents')->where('id', $p->id)->delete();
        }
    }

    /**
     * Gyerek kapcsolatok átemelése
     */
    protected function activateChildrenLinks(int $dogId, string $dogName): void
    {
        $children = DB::table('pedroo_children')
            ->where('parent_name', $dogName)
            ->get();

        foreach ($children as $c) {
            $childDogId = $this->findOrCreateDogByName($c->child_name);

            DB::table('children')->insert([
                'dog_id'       => $dogId,
                'child_id'     => $childDogId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            DB::table('pedroo_children')->where('id', $c->id)->delete();
        }
    }

    /**
     * Családfa kapcsolatok átemelése
     */
    protected function activateFamilyLinks(int $dogId, string $dogName): void
    {
        $families = DB::table('pedroo_families')
            ->where('dog_name', $dogName)
            ->get();

        foreach ($families as $f) {
            $relatedDogId = $this->findOrCreateDogByName($f->related_dog_name);

            DB::table('families')->insert([
                'dog_id'         => $dogId,
                'related_dog_id' => $relatedDogId,
                'relation_type'  => $f->relation_type, // pl. sibling, half-sibling
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('pedroo_families')->where('id', $f->id)->delete();
        }
    }

    /**
     * Segédfüggvény:
     * Megkeresi vagy létrehozza a kutyát név alapján
     */
    protected function findOrCreateDogByName(string $name): int
    {
        if (!$name) {
            return null;
        }

        // 1) Publikus dogs táblában van?
        $existing = DB::table('dogs')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) PedrooDog létezik?
        $pd = DB::table('pedroo_dogs')
            ->whereRaw('LOWER(real_name) = ?', [strtolower($name)])
            ->first();

        if ($pd) {
            // automatikus aktiválás
            $activation = new DogActivationService();
            return $activation->activateDog($pd->id);
        }

        // 3) Ha sehol nincs → létrehozunk egy minimális kutyát
        return DB::table('dogs')->insertGetId([
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}