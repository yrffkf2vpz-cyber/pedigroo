<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Championships;

class ChampionshipsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Championships $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Championships $model): bool { return true; }
    public function delete(User $user, Championships $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
