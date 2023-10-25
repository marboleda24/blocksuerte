<?php

namespace App\Mail;

use App\Traits\PdfTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Mpdf\MpdfException;

class SystemNotificationMail extends Mailable
{
    use Queueable, SerializesModels, PdfTrait;

    public string $title;
    public string $mail_subject;
    public string $message;
    public string $type;
    public array $another_info;
    public mixed $file_binary;

    /**
     * Create a new message instance.
     */
    public function __construct($title, $mail_subject, $message, $type = 'notify', $another_info = [], $file_binary = null)
    {
        $this->title = $title;
        $this->mail_subject = $mail_subject;
        $this->message = $message;
        $this->type = $type;
        $this->another_info = $another_info;
        $this->file_binary = $file_binary;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mail_subject,
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
            markdown: 'mail.notification',
            with: [
                'title' => $this->title,
                'message' => $this->message,
            ],
        );
    }

    /**
     * @return array
     * @throws MpdfException
     */
    public function attachments(): array
    {
        if ($this->type === 'payroll') {
            $pdf = $this->createPDF($this->another_info['nit'], $this->another_info['year'], $this->another_info['month'], $this->another_info['period'], $this->another_info['period_str']);

            return [
                Attachment::fromData(fn() => $pdf->output($this->another_info['filename'], 'S'), $this->another_info['filename'])
                    ->withMime('application/pdf'),
            ];
        }

        if ($this->file_binary){
            return [
                Attachment::fromData(fn() => $this->file_binary->output('file.pdf', 'S'), 'file.pdf')
                    ->withMime('application/pdf')
            ];
        }

        return [];
    }
}
