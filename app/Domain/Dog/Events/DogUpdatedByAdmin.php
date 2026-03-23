<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class DogUpdatedByAdmin
{
    use Dispatchable;

    public readonly array $rawDog;


    public function __construct(array $rawDog)
    {
        $this->rawDog = $rawDog;
    }
}