<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Parents;

class ParentsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Parents $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Parents $model): bool { return true; }
    public function delete(User $user, Parents $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
