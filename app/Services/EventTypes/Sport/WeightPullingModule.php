<?php

namespace App\Services\EventTypes\Sport;

use App\Services\EventTypes\BaseEventType;

class WeightPullingModule extends BaseEventType
{
    /**
     * Modul azonosító.
     */
    public string $key = 'weight_pulling';

    /**
     * Emberi olvasású név.
     */
    public string $name = 'Weight Pulling';

    /**
     * A modul mezodefiníciói.
     */
    public array $fields = [

        'judge' => [
            'type' => 'string',
            'required' => true,
            'label' => 'Judge Name',
        ],

        'dog_weight' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Dog Weight (kg)',
            'min' => 1,
        ],

        'pulled_weight' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Pulled Weight (kg)',
            'min' => 1,
        ],

        'pull_ratio' => [
            'type' => 'float',
            'required' => false,
            'label' => 'Pull Ratio (x bodyweight)',
            'min' => 0.01,
        ],

        'time' => [
            'type' => 'float',
            'required' => true,
            'label' => 'Pull Time (seconds)',
            'min' => 0.01,
        ],

        'disqualified' => [
            'type' => 'boolean',
            'required' => false,
            'label' => 'Disqualified',
        ],

        'reason' => [
            'type' => 'string',
            'required' => false,
            'label' => 'Disqualification Reason',
        ],

        'placement' => [
            'type' => 'integer',
            'required' => false,
            'label' => 'Placement',
            'min' => 1,
            'max' => 100,
        ],

        'notes' => [
            'type' => 'text',
            'required' => false,
            'label' => 'Judge Notes',
        ],
    ];

    /**
     * Canonicalizálás – egységes formára hozás.
     */
    public function canonicalize(array $data): array
    {
        foreach (['judge', 'reason', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Pull ratio automatikus számítása
        if (
            isset($data['dog_weight'], $data['pulled_weight']) &&
            !isset($data['pull_ratio'])
        ) {
            $data['pull_ratio'] = $data['pulled_weight'] / $data['dog_weight'];
        }

        // Ha nincs diszkvalifikáció, töröljük az okot
        if (isset($data['disqualified']) && $data['disqualified'] === false) {
            unset($data['reason']);
        }

        return $data;
    }

    /**
     * Validáció – a BaseEventType kezeli.
     */
    public function validate(array $data): array
    {
        return parent::validate($data);
    }
}