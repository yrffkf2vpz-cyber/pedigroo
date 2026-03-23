<?php

namespace App\Services;

use App\Enums\NameOrder;

class NameParser
{
    public function parse(string $name, int $order): array
    {
        $parts = explode(' ', trim($name));

        return match ($order) {

            NameOrder::REGISTERED_FIRST => [
                'prefix'        => $parts[0],
                'firstname'     => implode(' ', array_slice($parts, 1)),
                'lastname'      => null,
                'owner_kennel'  => null,
            ],

            NameOrder::CALL_FIRST => [
                'prefix'        => null,
                'firstname'     => $parts[0],
                'lastname'      => $parts[1] ?? null,
                'owner_kennel'  => null,
            ],

            NameOrder::PREFIX_CALL_SUFFIX => [
                'prefix'        => $parts[0],
                'firstname'     => implode(' ', array_slice($parts, 1, -1)),
                'lastname'      => null,
                'owner_kennel'  => end($parts),
            ],

            NameOrder::CALL_ONLY => [
                'prefix'        => null,
                'firstname'     => $name,
                'lastname'      => null,
                'owner_kennel'  => null,
            ],

            NameOrder::MULTIPLE_CALL_NAMES => [
                'prefix'        => null,
                'firstname'     => $name,
                'lastname'      => null,
                'owner_kennel'  => null,
            ],

            default => [
                'prefix'        => null,
                'firstname'     => $name,
                'lastname'      => null,
                'owner_kennel'  => null,
            ],
        };
    }
}

