<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\Models\Role;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Létrehoz egy új felhasználót.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            if (!empty($data['roles'])) {
                $this->assignRoles($user, $data['roles']);
            }

            // Timeline + Audit majd késobb jön
            return $user;
        });
    }

    /**
     * Frissíti a felhasználót.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name'  => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);

            if (isset($data['roles'])) {
                $this->syncRoles($user, $data['roles']);
            }

            return $user;
        });
    }

    /**
     * Szerepkörök hozzárendelése.
     */
    public function assignRoles(User $user, array $roles): void
    {
        $roleIds = Role::whereIn('name', $roles)->pluck('id');
        $user->roles()->attach($roleIds);
    }

    /**
     * Szerepkörök szinkronizálása.
     */
    public function syncRoles(User $user, array $roles): void
    {
        $roleIds = Role::whereIn('name', $roles)->pluck('id');
        $user->roles()->sync($roleIds);
    }

    /**
     * Szerepkör eltávolítása.
     */
    public function removeRole(User $user, string $role): void
    {
        $roleId = Role::where('name', $role)->value('id');
        if ($roleId) {
            $user->roles()->detach($roleId);
        }
    }
}
