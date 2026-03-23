<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Dog_sport_results;

class Dog_sport_resultsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Dog_sport_results $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Dog_sport_results $model): bool { return true; }
    public function delete(User $user, Dog_sport_results $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
