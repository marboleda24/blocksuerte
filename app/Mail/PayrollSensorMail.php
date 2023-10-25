<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayrollSensorMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $pending_count;

    public $success_count;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pending_count, $success_count)
    {
        $this->pending_count = $pending_count;
        $this->success_count = $success_count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): PayrollSensorMail
    {
        $icon = 'info';
        $title = 'Recordatorio de nomina electronica';
        $body = "Le informamos que a la fecha hay {$this->pending_count} documentos de nomina electronica pendientes por enviar a la DIAN y {$this->success_count} documentos enviados correctamente";

        return $this->subject('Recordatorio de nomina electronica')
            ->view('mail.notification', compact('icon', 'title', 'body'));
    }
}
