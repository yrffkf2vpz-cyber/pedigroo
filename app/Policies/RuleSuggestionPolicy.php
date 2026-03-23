<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RuleSuggestion;

class RuleSuggestionPolicy
{
    /**
     * Ki láthatja a listát?
     */
    public function viewAny(User $user)
    {
        return $user->isSuperAdmin() || $user->isBreedAdmin();
    }

    /**
     * Ki láthat egy konkrét rekordot?
     */
    public function view(User $user, RuleSuggestion $suggestion)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isBreedAdmin()) {
            return $suggestion->breed_id === $user->breed_id;
        }

        return false;
    }

    /**
     * Ki frissítheti (jóváhagyhatja/elutasíthatja)?
     */
    public function update(User $user, RuleSuggestion $suggestion)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isBreedAdmin()) {
            return $suggestion->breed_id === $user->breed_id;
        }

        return false;
    }
}
