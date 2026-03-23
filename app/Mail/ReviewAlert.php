<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewAlert extends Mailable
{
    use Queueable, SerializesModels;

    public string $type;
    public int $id;
    public ?string $name;
    public string $createdAt;
    public string $url;

    public function __construct(string $type, int $id, ?string $name, string $createdAt, string $url)
    {
        $this->type = $type;
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->url = $url;
    }

    public function build()
    {
        return $this
            ->subject('Új javítandó ' . $this->type . ' érkezett')
            ->view('emails.review_alert')
            ->text('emails.review_alert_plain');
    }
}