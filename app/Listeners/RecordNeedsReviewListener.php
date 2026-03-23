<?php

namespace App\Listeners;

use App\Events\RecordNeedsReview;
use App\Models\ReviewQueue;

class RecordNeedsReviewListener
{
    /**
     * Handle the event.
     */
    public function handle(RecordNeedsReview $event): void
    {
        $dog        = $event->dog;
        $normalized = $event->normalized;
        $reason     = $event->reason;

        // ---------------------------------------------------------
        // 1) Ne duplikáljunk: ha már van ilyen dog_id a queue-ban,
        //    akkor csak frissítjük az okot és a metaadatokat.
        // ---------------------------------------------------------
        $entry = ReviewQueue::firstOrNew([
            'dog_id' => $dog->id,
        ]);

        // ---------------------------------------------------------
        // 2) Alapadatok
        // ---------------------------------------------------------
        $entry->dog_id = $dog->id;
        $entry->reason = $reason;

        // ---------------------------------------------------------
        // 3) Metaadatok (audit + debug)
        // ---------------------------------------------------------
        $entry->meta = [
            'dog'        => [
                'id'            => $dog->id,
                'name'          => $dog->name,
                'reg_no_clean'  => $dog->reg_no_clean,
                'breed_id'      => $dog->breed_id,
                'origin_country'=> $dog->origin_country,
            ],
            'normalized' => $normalized,
            'timestamp'  => now()->toDateTimeString(),
        ];

        $entry->save();
    }
}