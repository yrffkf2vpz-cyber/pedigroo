<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Owners;

class OwnersPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Owners $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Owners $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function delete(User $user, Owners $model): bool
    {
        return false; // nincs törlés
    }

    public function deleteAny(User $user): bool
    {
        return false; // nincs tömeges törlés
    }
}