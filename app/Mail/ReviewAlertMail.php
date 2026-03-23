<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $model;
    public $type;

    public function __construct($model, string $type)
    {
        $this->model = $model;
        $this->type = $type;
    }

    public function build()
    {
        return $this->subject('New record needs review')
            ->view('emails.review-alert');
    }
}
