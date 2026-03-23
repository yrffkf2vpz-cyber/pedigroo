<?php

namespace App\Modules\AccessControl\Database\Factories;

use App\Models\Access\AccessAuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessAuditLogFactory extends Factory
{
    protected $model = AccessAuditLog::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'kennel_id' => 1,
            'dog_id' => null,
            'action' => $this->faker->randomElement(['attempt', 'allowed', 'denied', 'expired']),
            'reason' => $this->faker->optional()->sentence(),
            'created_at' => now(),
        ];
    }
}