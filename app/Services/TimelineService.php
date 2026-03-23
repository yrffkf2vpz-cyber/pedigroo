<?php

namespace App\Services;

use App\Models\User;
use App\Models\Timeline\UserTimeline;

class TimelineService
{
    public function recordUserTrustEvent(User $user, $event)
    {
        return UserTimeline::create([
            'user_id'     => $user->id,
            'type'        => 'trust_score',
            'title'       => 'Reputáció változás',
            'description' => "A reputációd {$event->amount} ponttal nott ({$event->type}).",
            'meta'        => [
                'score_added' => $event->amount,
                'event_type'  => $event->type,
                'meta'        => $event->meta,
            ],
        ]);
    }

    public function recordUserTokenEvent(User $user, string $title, string $description, array $meta = [])
    {
        return UserTimeline::create([
            'user_id'     => $user->id,
            'type'        => 'token',
            'title'       => $title,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }

    public function recordInvitationEvent(User $user, string $title, string $description, array $meta = [])
    {
        return UserTimeline::create([
            'user_id'     => $user->id,
            'type'        => 'invitation',
            'title'       => $title,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }

    public function recordAccessEvent(User $user, string $title, string $description, array $meta = [])
    {
        return UserTimeline::create([
            'user_id'     => $user->id,
            'type'        => 'access',
            'title'       => $title,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }

    public function recordDeviceEvent(User $user, string $title, string $description, array $meta = [])
    {
        return UserTimeline::create([
            'user_id'     => $user->id,
            'type'        => 'device',
            'title'       => $title,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }

    public function recordBreedingEvent(User $user, string $title, string $description, array $meta = [])
    {
        return UserTimeline::create([
            'user_id'     => $user->id,
            'type'        => 'breeding',
            'title'       => $title,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }
}

