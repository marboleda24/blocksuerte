<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefuseVacationByBossOrHumanResourceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $boss_name;

    public $observations;

    public function __construct($boss_name, $observations)
    {
        $this->boss_name = $boss_name;
        $this->observations = $observations;
    }

    public function build(): RefuseVacationByBossOrHumanResourceMail
    {
        return $this->subject('Solicitud de vacaciones rechazada')
            ->view('emails.RefuseVacationByBossOrHumanResource');
    }
}
