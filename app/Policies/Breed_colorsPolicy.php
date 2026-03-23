<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Breed_colors;

class Breed_colorsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Breed_colors $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Breed_colors $model): bool { return true; }
    public function delete(User $user, Breed_colors $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
