<?php

namespace App\Services\Vet;

use Illuminate\Support\Facades\DB;

class HealthCertificateService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_health_certificates')
            ->where('dog_id', $dogId)
            ->orderBy('issued_at', 'desc')
            ->get()
            ->toArray();
    }
}
