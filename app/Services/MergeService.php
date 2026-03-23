<?php

namespace App\Services;

use App\Models\Dog;
use App\Models\Kennel;

class MergeService
{
    public function mergeDogs(int $a, int $b): void
    {
        $A = Dog::findOrFail($a);
        $B = Dog::findOrFail($b);

        // Csak a legfontosabb mezők (B opció)
        $A->name = $A->name ?: $B->name;
        $A->breed = $A->breed ?: $B->breed;
        $A->reg_no = $A->reg_no ?: $B->reg_no;

        $A->save();
        $B->delete();
    }

    public function mergeKennels(int $a, int $b): void
    {
        $A = Kennel::findOrFail($a);
        $B = Kennel::findOrFail($b);

        // Csak a legfontosabb mezők (B opció)
        $A->kennel_name = $A->kennel_name ?: $B->kennel_name;
        $A->country = $A->country ?: $B->country;

        $A->save();
        $B->delete();
    }
}