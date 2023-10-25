<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RetrievePasswordCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customer, $password)
    {
        $this->customer = $customer;
        $this->customer->newpassword = str_replace('/', '(slash)', $this->customer->newpassword);
        $this->password = $password;
    }

    public function build()
    {
        if (env('MAIL_USERNAME')) {
            return $this->view('mails.mailretrievepasswordcustomer')
            ->subject('Recuperacion de Credenciales de Ingreso a la plataforma '.env('APP_NAME'))
            ->from(env('MAIL_USERNAME'), env('APP_NAME'));
        } else {
            return $this->view('mails.mailretrievepasswordcustomer')
            ->subject('Recuperacion de Credenciales de Ingreso a la plataforma '.env('APP_NAME'))
            ->from(config('mail.username'), env('APP_NAME'));
        }
    }
}
