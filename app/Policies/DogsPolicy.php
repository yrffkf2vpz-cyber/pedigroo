<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Dog;
use App\Models\PendingDog;

class DogsPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Dog $dog): bool
    {
        return true;
    }

    public function update(User $user, Dog $dog): bool
    {
        return $user->id === $dog->current_owner_id;
    }

    public function activate(User $user, PendingDog $pending): bool
    {
        return $user->id === $pending->current_owner_id;
    }

    public function unpublish(User $user, Dog $dog): bool
    {
        return $user->id === $dog->current_owner_id;
    }

    public function delete(User $user, Dog $dog): bool
    {
        return false; // nincs törlés
    }

    public function deleteAny(User $user): bool
    {
        return false; // nincs tömeges törlés
    }
}