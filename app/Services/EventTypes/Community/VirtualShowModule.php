<?php

namespace App\Services\EventTypes\Community;

use App\Services\EventTypes\BaseEventType;

class VirtualShowModule extends BaseEventType
{
    public string $key = 'virtual_show';
    public string $name = 'Virtual Show';

    public array $fields = [

        'show_name' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Show Name',
        ],

        'class_entered' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Class Entered',
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 10000,
        ],

        'score' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Score',
            'min' => 0,
            'max' => 100,
        ],

        'judge' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Judge',
        ],

        'platform' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Platform (e.g. Facebook, YouTube)',
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Notes',
        ],
    ];

    public function canonicalize(array $data): array
    {
        foreach (['show_name','class_entered','judge','platform','notes'] as $f) {
            if (isset($data[$f])) $data[$f] = trim($data[$f]);
        }
        return $data;
    }
}