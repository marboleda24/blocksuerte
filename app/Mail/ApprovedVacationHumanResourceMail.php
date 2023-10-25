<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedVacationHumanResourceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $start_date;

    public $end_date;

    public $boss_name;

    /**
     * @param $start_date
     * @param $end_date
     * @param $boss_name
     */
    public function __construct($start_date, $end_date, $boss_name)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->boss_name = $boss_name;
    }

    /**
     * @return ApprovedVacationHumanResourceMail
     */
    public function build(): ApprovedVacationHumanResourceMail
    {
        $icon = 'success';
        $title = 'Vacaciones aprobadas';
        $body = "Nos complace informale que la solicitud de vacaciones comprendida entre {$this->start_date} y {$this->end_date} ha sido aprobada";

        return $this->subject('Aprobación  de vacaciones')
        ->view('mail.notification', compact('title', 'body', 'icon'));
    }
}
