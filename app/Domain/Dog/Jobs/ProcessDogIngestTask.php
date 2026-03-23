<?php

namespace App\Jobs;

use App\Models\DogIngestTask;
use App\Services\DogNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDogIngestTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $taskId)
    {
    }

    public function handle(DogNormalizer $normalizer): void
    {
        $task = DogIngestTask::find($this->taskId);

        if (!$task) {
            return;
        }

        $dog = $task->dog;

        if (!$dog) {
            $task->status = 'error';
            $task->last_error = 'Missing pedroo_dogs record';
            $task->save();
            return;
        }

        $pdDog = $normalizer->normalize($dog);

        if (!$pdDog) {
            $task->status = 'needs_review';
            $task->save();
            return;
        }

        $task->status = 'done';
        $task->save();
    }
}
