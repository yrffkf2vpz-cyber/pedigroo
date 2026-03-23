<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Test_table;

class Test_tablePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Test_table $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Test_table $model): bool { return true; }
    public function delete(User $user, Test_table $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
