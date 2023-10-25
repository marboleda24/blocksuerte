<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaintenanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $created_name;

    public $asset_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($created_name, $asset_name)
    {
        $this->created_name = $created_name;
        $this->asset_name = $asset_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): MaintenanceMail
    {
        return $this->subject('Notificacion de mantenimiento')
            ->view('emails.MaintenanceMail');
    }
}
