<?php

namespace Database\Factories\Breeding;

use App\Models\Breeding\BuyerAccessRequest;
use App\Models\User;
use App\Models\Dog;
use App\Models\Kennel;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuyerAccessRequestFactory extends Factory
{
    protected $model = BuyerAccessRequest::class;

    public function definition(): array
    {
        return [
            'buyer_id' => User::factory(),
            'dog_id' => Dog::factory(),
            'kennel_id' => Kennel::factory(),

            'purpose' => $this->faker->randomElement(['pet_home', 'breeding', 'show', 'other']),
            'message' => $this->faker->sentence(),
            'status' => 'pending',

            'ip_address' => $this->faker->ipv4(),
            'device_fingerprint' => $this->faker->uuid(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}