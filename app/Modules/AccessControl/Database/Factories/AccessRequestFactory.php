<?php

namespace App\Modules\AccessControl\Database\Factories;

use App\Models\Access\AccessRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessRequestFactory extends Factory
{
    protected $model = AccessRequest::class;

    public function definition()
    {
        return [
            'requester_user_id' => 1, // teszt user
            'kennel_id' => 1,
            'dog_id' => null,
            'request_type' => $this->faker->randomElement([
                'view_details',
                'view_pedigree',
                'view_litter',
                'view_private_photos'
            ]),
            'message' => $this->faker->optional()->sentence(),
            'status' => 'pending',
        ];
    }
}