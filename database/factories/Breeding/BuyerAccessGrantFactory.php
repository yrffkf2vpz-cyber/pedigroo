<?php

namespace Database\Factories\Breeding;

use App\Models\Breeding\BuyerAccessGrant;
use App\Models\Breeding\BuyerAccessRequest;
use App\Models\User;
use App\Models\Dog;
use App\Models\Kennel;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuyerAccessGrantFactory extends Factory
{
    protected $model = BuyerAccessGrant::class;

    public function definition(): array
    {
        return [
            'request_id' => BuyerAccessRequest::factory(),
            'buyer_id' => User::factory(),
            'dog_id' => Dog::factory(),
            'kennel_id' => Kennel::factory(),

            'expires_at' => now()->addDays(30),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => now()->subDays(1),
        ]);
    }
}