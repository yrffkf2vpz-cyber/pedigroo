<?php

namespace App\Modules\AccessControl\Database\Factories;

use App\Models\Access\UserVisibilityOverride;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserVisibilityOverrideFactory extends Factory
{
    protected $model = UserVisibilityOverride::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'kennel_id' => 1,
            'allowed_fields' => ['pedigree', 'health'],
        ];
    }
}