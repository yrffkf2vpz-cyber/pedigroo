<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Dog_working_results;

class Dog_working_resultsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Dog_working_results $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Dog_working_results $model): bool { return true; }
    public function delete(User $user, Dog_working_results $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
