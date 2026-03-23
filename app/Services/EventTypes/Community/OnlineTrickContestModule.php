<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class OnlineTrickContestModule extends BaseEventType
{
    public string $key = 'online_trick_contest';
    public string $name = 'Online Trick Contest';

    public array $fields = [

        'contest_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Contest Name',
        ],

        'platform' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Platform (e.g. Facebook, Instagram)',
        ],

        'trick_performed' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Trick Performed',
        ],

        'score' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Score',
            'min' => 0,
            'max' => 100,
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 10000,
        ],

        'judge' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Judge',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['contest_name','platform','trick_performed','judge','notes'] as $f) {
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        }
        return $data;
    }
}