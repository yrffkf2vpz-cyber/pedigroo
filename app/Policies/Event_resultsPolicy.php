<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event_results;

class Event_resultsPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Event_results $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Event_results $model): bool { return true; }
    public function delete(User $user, Event_results $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
