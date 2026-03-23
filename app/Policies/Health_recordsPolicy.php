<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Health_records;

class Health_recordsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Health_records $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Health_records $model): bool { return true; }
    public function delete(User $user, Health_records $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
