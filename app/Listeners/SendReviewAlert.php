<?php

namespace App\Listeners;

use App\Events\RecordNeedsReview;
use App\Mail\ReviewAlertMail;
use App\Services\AdminLookupService;
use Illuminate\Support\Facades\Mail;

class SendReviewAlert
{
    public function handle(RecordNeedsReview $event)
    {
        $adminEmail = AdminLookupService::resolveAdminEmail($event->model, $event->type);

        if ($adminEmail) {
            Mail::to($adminEmail)->send(new ReviewAlertMail($event->model, $event->type));
        }
    }
}
