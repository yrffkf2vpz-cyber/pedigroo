<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class DogCreatedByUser
{
    use Dispatchable;

    public array $rawDog;

    public function __construct(array $rawDog)
    {
        $this->rawDog = $rawDog;
    }
}