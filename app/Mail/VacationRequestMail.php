<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VacationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee_name;

    public $start_date;

    public $end_date;

    public $approved_url;

    public $refuse_url;

    public $justify;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employee_name, $start_date, $end_date, $approved_url, $refuse_url, $justify)
    {
        $this->employee_name = $employee_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->approved_url = $approved_url;
        $this->refuse_url = $refuse_url;
        $this->justify = $justify;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): VacationRequestMail
    {
        return $this->subject('Solicitud de vacaciones')
            ->view('emails.VacationRequestMail');
    }
}
