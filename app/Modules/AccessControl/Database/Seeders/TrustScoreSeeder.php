<?php

namespace App\Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Access\UserTrustScore;

class TrustScoreSeeder extends Seeder
{
    public function run(): void
    {
        UserTrustScore::factory()->count(10)->create();
    }
}