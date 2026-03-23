<?php

namespace App\Modules\AccessControl\Database\Factories;

use App\Models\Access\UserTrustScore;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserTrustScoreFactory extends Factory
{
    protected $model = UserTrustScore::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'score' => $this->faker->numberBetween(0, 100),
            'level' => $this->faker->randomElement(['green', 'yellow', 'red']),
            'last_update' => now(),
        ];
    }
}