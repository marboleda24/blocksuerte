<?php

namespace App\Jobs;

use App\Mail\ProofPaymentMailGoja;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendProofPaymentMailQueueEmailGoja implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * @var
     */
    protected $pdf_data;

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
     * Execute the job.
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle()
    {
        try {
            $email = new ProofPaymentMailGoja($this->pdf_data);
            Mail::to($this->pdf_data['mail_address'])->send($email);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}
