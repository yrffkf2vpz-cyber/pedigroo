<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pedroo_registry;

class Pedroo_registryPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Pedroo_registry $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Pedroo_registry $model): bool { return true; }
    public function delete(User $user, Pedroo_registry $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
