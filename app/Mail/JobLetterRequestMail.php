<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobLetterRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $file;

    public $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($file, $filename)
    {
        $this->file = $file;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): JobLetterRequestMail
    {
        return $this->subject('Carta laboral')
            ->view('emails.JobLetterRequestMail')
            ->attachData($this->file->output(), $this->filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
