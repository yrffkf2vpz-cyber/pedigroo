<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Learning_aliases;

class Learning_aliasesPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Learning_aliases $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Learning_aliases $model): bool { return true; }
    public function delete(User $user, Learning_aliases $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
