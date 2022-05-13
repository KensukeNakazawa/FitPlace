<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    private string $title;
    private string $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $title, string $text)
    {
        $this->title = sprintf('%s', $title);
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.register')
            ->subject($this->title)
            ->with([
                       'text' => $this->text,
                   ]);
    }
}
