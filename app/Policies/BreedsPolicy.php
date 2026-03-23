<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Breeds;

class BreedsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Breeds $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Breeds $model): bool { return true; }
    public function delete(User $user, Breeds $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
