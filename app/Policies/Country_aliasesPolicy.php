<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Country_aliases;

class Country_aliasesPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Country_aliases $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Country_aliases $model): bool { return true; }
    public function delete(User $user, Country_aliases $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
