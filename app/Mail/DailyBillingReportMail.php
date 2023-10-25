<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyBillingReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var object
     */
    public object $national, $ci, $total, $current_month, $last_year;

    /**
     * Create a new message instance.
     */
    public function __construct($national, $ci, $total, $current_month, $last_year)
    {
        $this->national = $national;
        $this->ci = $ci;
        $this->total = $total;
        $this->current_month = $current_month;
        $this->last_year = $last_year;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte de Facturación Diaria',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.daily-billing-report',
            with: [
                'date' => Carbon::now()->format('Y-m-d'),
                'national' => $this->national,
                'ci' => $this->ci,
                'total' => $this->total,
                'current_month' => $this->current_month,
                'last_year' => $this->last_year,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
