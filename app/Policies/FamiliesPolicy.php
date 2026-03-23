<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Families;

class FamiliesPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Families $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Families $model): bool { return true; }
    public function delete(User $user, Families $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
