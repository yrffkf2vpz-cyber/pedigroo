<?php

namespace App\Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Access\AccessRequest;

class AccessRequestSeeder extends Seeder
{
    public function run(): void
    {
        AccessRequest::factory()->count(10)->create();
    }
}