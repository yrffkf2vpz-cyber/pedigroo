<?php

namespace App\Services;

use App\Models\PedrooDog;
use App\Models\PdDog;

class DogNormalizer
{
    public function normalize(PedrooDog $dog): ?PdDog
    {
        $resolver = app(NameOrderResolver::class);
        $parser   = app(NameParser::class);

        $order = $resolver->resolve($dog);

        if (!$order) {
            return null; // admin review
        }

        $parsed = $parser->parse($dog->real_name, $order);

        return PdDog::create([
            'prefix'        => $parsed['prefix'],
            'firstname'     => $parsed['firstname'],
            'lastname'      => $parsed['lastname'],
            'owner_kennel'  => $parsed['owner_kennel'],
            'name_order_id' => $order,
            'breed_id'      => $dog->breed_id,
            'sex'           => $dog->sex,
            'dob'           => $dog->dob,
            'reg_no'        => $dog->reg_no,
            'owner_id'      => $dog->owner_id,
            'breeder_id'    => $dog->breeder_id,
        ]);
    }
}

