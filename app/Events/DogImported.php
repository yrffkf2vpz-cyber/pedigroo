<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class DogImported
{
    use Dispatchable;

    public array $rawDog;

    public function __construct(array $rawDog)
    {
        $this->rawDog = $rawDog;
    }
}