<?php

namespace App\Repositories\Storage;

use Illuminate\Support\Facades\DB;

class TngDogRepository
{
    public function all()
    {
        return DB::connection('storage')
            ->table('tng_dogs')
            ->get();
    }

    public function find(int $id)
    {
        return DB::connection('storage')
            ->table('tng_dogs')
            ->where('id', $id)
            ->first();
    }
}
