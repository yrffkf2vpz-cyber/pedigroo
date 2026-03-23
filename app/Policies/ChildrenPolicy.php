<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Children;

class ChildrenPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Children $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Children $model): bool { return true; }
    public function delete(User $user, Children $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
