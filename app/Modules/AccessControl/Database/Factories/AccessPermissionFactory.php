<?php

namespace App\Modules\AccessControl\Database\Factories;

use App\Models\Access\AccessPermission;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessPermissionFactory extends Factory
{
    protected $model = AccessPermission::class;

    public function definition()
    {
        return [
            'request_id' => 1,
            'granted_by_user_id' => 1,
            'allowed_fields' => ['pedigree', 'health'],
            'expires_at' => now()->addDays(7),
        ];
    }
}