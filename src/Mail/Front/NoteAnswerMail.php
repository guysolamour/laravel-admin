<?php

namespace Guysolamour\Administrable\Mail\Front;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoteAnswerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $note;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, $note)
    {
        $this->subject = $subject;
        $this->note    = $note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(config('mail.from.address'))
            ->subject($this->subject)
            ->markdown('administrable::emails.back.note');
    }
}
