<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Kennels;

class KennelsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Kennels $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Kennels $model): bool { return true; }
    public function delete(User $user, Kennels $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
