<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PendingDog;

class PendingDogPolicy
{
    public function view(User $user, PendingDog $dog): bool
    {
        return $user->id === $dog->current_owner_id;
    }

    public function update(User $user, PendingDog $dog): bool
    {
        return $user->id === $dog->current_owner_id;
    }

    public function activate(User $user, PendingDog $dog): bool
    {
        return $user->id === $dog->current_owner_id;
    }

    public function delete(User $user, PendingDog $dog): bool
    {
        return false; // nincs törlés
    }
}