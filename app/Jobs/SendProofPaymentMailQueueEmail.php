<?php

namespace App\Jobs;

use App\Mail\SystemNotificationMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendProofPaymentMailQueueEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * @var array
     */
    protected array $pdf_data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pdf_data)
    {
        $this->pdf_data = $pdf_data;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            Mail::to($this->pdf_data['mail_address'])
                ->send(new SystemNotificationMail("COMPROBANTE DE NOMINA", "COMPROBANTE DE NOMINA {$this->pdf_data['period_str']}", "Adjunto encontrara su comprobante de nomina {$this->pdf_data['period_str']}", 'payroll', $this->pdf_data));
        } catch (Exception $e) {
            print_r("{$e->getLine()} – {$e->getFile()} – {$e->getMessage()}");
        }
    }
}
