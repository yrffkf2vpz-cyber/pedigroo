<?php

namespace App\Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Access\AccessPermission;

class AccessPermissionSeeder extends Seeder
{
    public function run(): void
    {
        AccessPermission::factory()->count(10)->create();
    }
}