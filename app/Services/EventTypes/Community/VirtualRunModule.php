<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class VirtualRunModule extends BaseEventType
{
    public string $key = 'virtual_run';
    public string $name = 'Virtual Run';

    public array $fields = [

        'event_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Event Name',
        ],

        'distance_km' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Distance (km)',
            'min' => 0.1,
            'max' => 200,
        ],

        'time_minutes' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Time (minutes)',
            'min' => 1,
            'max' => 20000,
        ],

        'platform' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Platform (e.g. Strava, Garmin)',
        ],

        'verified' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Verified Result',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['event_name','platform','notes'] as $f) {
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        }

        // Ha nincs ido ? verified = false
        if (empty($data['time_minutes'])) {
            $data['verified'] = false;
        }

        return $data;
    }
}