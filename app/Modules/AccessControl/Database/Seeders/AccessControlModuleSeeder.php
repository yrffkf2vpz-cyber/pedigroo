<?php

namespace App\Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;

class AccessControlModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccessRequestSeeder::class,
            AccessPermissionSeeder::class,
            TrustScoreSeeder::class,
        ]);
    }
}