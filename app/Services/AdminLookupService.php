<?php

namespace App\Services;

use App\Models\User;

class AdminLookupService
{
    public static function resolveAdminEmail($model, string $type): ?string
    {
        // 1) fajta admin (ha van)
        if ($type === 'dog' && $model->breed && $model->breed->admin_email) {
            return $model->breed->admin_email;
        }

        // 2) kennel admin (ha lenne, de nálunk nincs)
        // kihagyva

        // 3) fallback: fo admin
        return User::where('email', 'admin@pedigroo.com')->value('email');
    }
}
