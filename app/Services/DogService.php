<?php

namespace App\Services;

use App\Models\Dog;
use App\Models\PendingDog;
use Carbon\Carbon;

class DogService
{
    public function storeNormalizedDog(array $dog)
    {
        $ageInDays = $this->calculateAgeInDays($dog);

        if ($ageInDays >= 15 * 365) {
            return $this->storePublicDog($dog);
        }

        return $this->storePendingDog($dog);
    }

    private function calculateAgeInDays(array $dog): int
    {
        if (!empty($dog['dob'])) {
            return now()->diffInDays($dog['dob']);
        }

        if (!empty($dog['reg_date'])) {
            return now()->diffInDays($dog['reg_date']);
        }

        if (!empty($dog['reg_year'])) {
            $date = Carbon::create($dog['reg_year'], 12, 31);
            return now()->diffInDays($date);
        }

        return 999999; // ha semmi nincs, publikálható
    }

    public function storePublicDog(array $dog)
    {
        $public = new Dog();
        $public->fill($dog);
        $public->save();

        return $public;
    }

    public function storePendingDog(array $dog)
    {
        $pending = new PendingDog();
        $pending->fill($dog);

        if (!empty($dog['dob'])) {
            $pending->protected_until = Carbon::parse($dog['dob'])->addYears(15);
        } elseif (!empty($dog['reg_date'])) {
            $pending->protected_until = Carbon::parse($dog['reg_date'])->addYears(15);
        } elseif (!empty($dog['reg_year'])) {
            $pending->protected_until = Carbon::create($dog['reg_year'], 12, 31)->addYears(15);
        } else {
            $pending->protected_until = now();
        }

        $pending->activation_status = 'pending';
        $pending->pending_reason = 'younger_than_15_years';

        $pending->save();

        return $pending;
    }

    public function movePendingToPublic(PendingDog $pending)
    {
        $public = new Dog();
        $public->fill($pending->toArray());
        $public->save();

        $pending->activation_status = 'activated';
        $pending->save();
        $pending->delete();

        return $public;
    }

    public function autoPublish()
    {
        $today = now()->toDateString();

        $list = PendingDog::where('activation_status', 'pending')
            ->whereDate('protected_until', '<=', $today)
            ->get();

        foreach ($list as $pendingDog) {
            $this->movePendingToPublic($pendingDog);
        }

        return $list->count();
    }

    public function unpublishDog(Dog $dog, $user)
    {
        if ($dog->current_owner_id !== $user->id) {
            abort(403);
        }

        $pending = new PendingDog();
        $pending->fill($dog->toArray());
        $pending->activation_status = 'pending';
        $pending->pending_reason = 'owner_unpublished';
        $pending->protected_until = now()->addYears(15);
        $pending->save();

        $dog->delete();

        return $pending;
    }
}