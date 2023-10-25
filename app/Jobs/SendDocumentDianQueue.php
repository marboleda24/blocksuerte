<?php

namespace App\Jobs;

use App\Models\ElectronicBillingLog;
use App\Models\SystemSetting;
use App\Traits\ElectronicBillingTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Throwable;

class SendDocumentDianQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ElectronicBillingTrait, IsMonitored;

    protected int $invoice;

    protected string $type;

    protected int $sendby;

    protected string $entity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoice, $type, $sendby, $entity)
    {
        $this->invoice = $invoice;
        $this->type = $type;
        $this->sendby = $sendby;
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws GuzzleException|Throwable
     */
    public function handle(): void
    {
        try {
            $this->queueProgress(0);
            $result = [];

            $checkReason = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
                ->where('NUMERO', '=', $this->invoice)
                ->pluck('MOTIVO')
                ->first();

            if ($checkReason === '39') {
                $result = json_decode($this->sendInvoiceDianUSD($this->invoice, $this->entity));
            } else {
                $result = match ($this->type) {
                    'invoice' => json_decode($this->sendInvoiceDian($this->invoice, $this->entity)),
                    'credit_note_export' => json_encode($this->sendCreditNoteExportDian($this->invoice, $this->entity)),
                    'credit_note' => json_decode($this->sendCreditNoteDian($this->invoice, $this->entity)),
                    'debit_note' => json_decode($this->sendDebitNoteDian($this->invoice, $this->entity)),
                    'invoice_export' => json_decode($this->sendInvoiceExportDian($this->invoice, $this->entity)),
                    default => throw new Exception('No se encontró un tipo de documento valido', 500),
                };
            }

            $this->queueProgress(50);

            $message = $result->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->ErrorMessage->string ?? '';

            if (is_array($message)) {
                $message_str = implode('|', $message);
            } else {
                $message_str = $message;
            }

            $system = SystemSetting::find($this->entity === 'CIEV' ? 1 : 2);
            $result_dms = [];

            if ($system->automatic_import_DMS_MAX) {
                if ($this->type === 'invoice' && $result->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid === 'true') {
                    $result_dms = $this->import_document_dms($this->invoice, $this->sendby, $checkReason === '39' ? '39' : ($checkReason === '38' ? '38': null), $this->entity);
                } elseif ($this->type === 'invoice_export' && $result->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid === 'true') {
                    $result_dms = $this->import_document_dms($this->invoice, $this->sendby, null, $this->entity);
                } elseif ($this->type === 'credit_note' && $result->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid === 'true') {
                    if ($this->entity === 'GOJA') {
                        $result_dms = $this->import_credit_note_dms($this->invoice, $this->sendby);
                    } else {
                        $result_dms['msg'] = 'Documento electrónico no es una factura o con error en campos mandatorios';
                    }
                } else {
                    $result_dms['msg'] = 'Documento electrónico no es una factura o con error en campos mandatorios';
                }
            } else {
                $result_dms['msg'] = 'Importación automática desactivada';
            }

            ElectronicBillingLog::create([
                'document' => $this->invoice,
                'status' => $result->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid,
                'status_code' => $result->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->StatusCode,
                'error_message' => "DIAN: $message_str DMS: {$result_dms['msg']}",
                'send_id' => $this->sendby,
            ]);

            $this->queueProgress(100);
        } catch (Exception $e) {
            ElectronicBillingLog::create([
                'document' => $this->invoice,
                'status' => 'Failed',
                'status_code' => '500',
                'error_message' => $e->getMessage(),
                'send_id' => $this->sendby,
            ]);
        }


    }
}
