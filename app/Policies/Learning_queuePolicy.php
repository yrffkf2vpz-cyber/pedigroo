<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Learning_queue;

class Learning_queuePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Learning_queue $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Learning_queue $model): bool { return true; }
    public function delete(User $user, Learning_queue $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
