<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Diagnosis_aliases;

class Diagnosis_aliasesPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Diagnosis_aliases $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Diagnosis_aliases $model): bool { return true; }
    public function delete(User $user, Diagnosis_aliases $model): bool { return true; }
    public function deleteAny(User $user): bool { return true; }
}
