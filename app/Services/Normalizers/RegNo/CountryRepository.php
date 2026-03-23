<?php

namespace App\Services\Normalizers\RegNo;

use App\Models\Country;

class CountryRepository
{
    public function all(): array
    {
        return Country::all()->keyBy('code')->toArray();
    }

    public function get(string $code): ?array
    {
        return Country::where('code', $code)->first()?->toArray();
    }
}