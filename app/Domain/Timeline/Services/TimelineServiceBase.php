<?php

namespace App\Services\Timeline;

use Illuminate\Database\Eloquent\Model;

abstract class TimelineServiceBase
{
    abstract protected function model(): Model;

    public function addEvent(
        int $entityId,
        string $eventType,
        array $data = [],
        ?string $timestamp = null
    ): Model {
        $model = $this->model();

        return $model->create([
            $this->entityKey() => $entityId,
            'event_type' => $eventType,
            'timestamp' => $timestamp ?? now(),
            'data' => $data,
        ]);
    }

    protected function entityKey(): string
    {
        return $this->model()->getForeignKey();
    }
}