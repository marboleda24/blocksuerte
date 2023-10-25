<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class InvoiceAuditMail extends Mailable
{
    use Queueable, SerializesModels;

    public bool $state;
    public array $errors;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($state, $errors = [])
    {
        $this->state = $state;
        $this->errors = $errors;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación de auditoría de facturación',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.markdown.invoice-audit',
            with: [
                'state' => $this->state,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        if (!$this->state && count($this->errors) > 0) {
            $pdf = $this->generatePDF($this->errors);
            return [
                Attachment::fromData(fn() => $pdf->output(), Str::random(48))
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }

    /**
     * @param $errors
     * @return \Barryvdh\DomPDF\PDF
     */
    protected function generatePDF($errors): \Barryvdh\DomPDF\PDF
    {
        $imgBase64 = base64_encode(file_get_contents(public_path('img/8909266178.jpg')));
        $imgBase64 = 'data:image/png;base64, ' . $imgBase64;
        $pdf = PDF::loadView('pdfs.invoice-audit', compact('errors', 'imgBase64'));
        $pdf->setWarnings(false);
        $pdf->setPaper('letter', 'landscape');

        return $pdf;
    }
}
