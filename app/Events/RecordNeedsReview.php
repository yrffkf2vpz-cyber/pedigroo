<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecordNeedsReview
{
    use Dispatchable, SerializesModels;

    public readonly object $model;
    public readonly string $type;


    public function __construct($model, string $type)
    {
        $this->model = $model;
        $this->type = $type; // 'dog' vagy 'kennel'
    }
}
