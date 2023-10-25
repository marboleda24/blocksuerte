<?php

namespace App\Mail;

use App\Traits\GojaPdfTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProofPaymentMailGoja extends Mailable
{
    use Queueable, SerializesModels, GojaPdfTrait;

    public $pdf_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf_data)
    {
        $this->pdf_data = $pdf_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ProofPaymentMailGoja
    {
        try {
            $pdf = $this->createPDF($this->pdf_data['nit'], $this->pdf_data['year'], $this->pdf_data['month'], $this->pdf_data['period'], $this->pdf_data['period_str']);

            return $this->subject('Comprobante de nomina Plasticos GOJA')
                ->view('emails.ProofPaymentMail')
                ->attachData($pdf->output($this->pdf_data['filename'], 'S'), $this->pdf_data['filename'], [
                    'mime' => 'application/pdf',
                ]);
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}
