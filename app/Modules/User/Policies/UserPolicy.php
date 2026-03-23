<?php

namespace App\Modules\User\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Meghatározza, hogy a rendszerben van-e aktív fajta admin.
     */
    protected function breedAdminExists(): bool
    {
        return User::whereNotNull('breed_id')->exists();
    }

    /**
     * Super Admin mindig mindent megtehet.
     */
    protected function isSuperAdmin(User $user): bool
    {
        return (bool) $user->super_admin;
    }

    /**
     * A user láthat-e egy másik usert.
     */
    public function view(User $actor, User $target): bool
    {
        // Super Admin ? mindig igen
        if ($this->isSuperAdmin($actor)) {
            return true;
        }

        // Ha nincs fajta admin ? Super Admin mód ? csak Super Admin láthat
        if (!$this->breedAdminExists()) {
            return false;
        }

        // Breed Admin ? csak a saját fajtáját láthatja
        if ($actor->breed_id && $actor->breed_id === $target->breed_id) {
            return true;
        }

        return false;
    }

    /**
     * A user módosíthat-e egy másik usert.
     */
    public function update(User $actor, User $target): bool
    {
        // Super Admin ? mindig igen
        if ($this->isSuperAdmin($actor)) {
            return true;
        }

        // Ha nincs fajta admin ? Super Admin mód ? csak Super Admin módosíthat
        if (!$this->breedAdminExists()) {
            return false;
        }

        // Breed Admin ? csak a saját fajtáját módosíthatja
        if ($actor->breed_id && $actor->breed_id === $target->breed_id) {
            return true;
        }

        return false;
    }

    /**
     * A user adhat-e szerepkört egy másik usernek.
     */
    public function assignRoles(User $actor, User $target): bool
    {
        // Super Admin ? mindig igen
        if ($this->isSuperAdmin($actor)) {
            return true;
        }

        // Ha nincs fajta admin ? Super Admin mód ? csak Super Admin adhat szerepkört
        if (!$this->breedAdminExists()) {
            return false;
        }

        // Breed Admin ? csak a saját fajtájának adhat szerepkört
        if ($actor->breed_id && $actor->breed_id === $target->breed_id) {
            return true;
        }

        return false;
    }

    /**
     * A user törölhet-e egy másik usert.
     */
    public function delete(User $actor, User $target): bool
    {
        // Super Admin ? mindig igen
        if ($this->isSuperAdmin($actor)) {
            return true;
        }

        // Ha nincs fajta admin ? Super Admin mód ? csak Super Admin törölhet
        if (!$this->breedAdminExists()) {
            return false;
        }

        // Breed Admin ? csak a saját fajtáját törölheti
        if ($actor->breed_id && $actor->breed_id === $target->breed_id) {
            return true;
        }

        return false;
    }
}
