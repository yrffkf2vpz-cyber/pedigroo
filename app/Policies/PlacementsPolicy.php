<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Placements;

class PlacementsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Placements $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Placements $model): bool { return true; }
    public function delete(User $user, Placements $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
