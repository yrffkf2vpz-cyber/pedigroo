<?php

namespace App\Enums;

class NameOrder
{
    public const CALL_FIRST            = 1;
    public const REGISTERED_FIRST      = 2;
    public const PREFIX_CALL_SUFFIX    = 3;
    public const CALL_ONLY             = 4;
    public const MULTIPLE_CALL_NAMES   = 5;

    public static function label(int $id): string
    {
        return match ($id) {
            self::CALL_FIRST            => 'Call First',
            self::REGISTERED_FIRST      => 'Registered First',
            self::PREFIX_CALL_SUFFIX    => 'Prefix + Call + Suffix',
            self::CALL_ONLY             => 'Call Only',
            self::MULTIPLE_CALL_NAMES   => 'Multiple Call Names',
            default                     => 'Unknown',
        };
    }
}
