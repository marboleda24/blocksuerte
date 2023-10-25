<?php

namespace App\Jobs;

use App\Models\PayrollDocument;
use App\Models\PayrollLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class PayrollQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $response_api;

    public $user_id;

    public $current_id;

    public $year;

    public $month;

    public $start_period;

    public $end_period;

    public $entity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($year, $month, $start_period, $end_period, $user_id, $entity = 'CIEV')
    {
        $this->year = $year;
        $this->month = $month;
        $this->start_period = $start_period;
        $this->end_period = $end_period;
        $this->user_id = $user_id;
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws GuzzleException
     */
    public function handle(): void
    {
        try {
            $list = PayrollDocument::where('entity', '=', $this->entity)
                ->where('year', '=', $this->year)
                ->where('month', '=', $this->month)
                ->where('start_period', '=', $this->start_period)
                ->where('end_period', '=', $this->end_period)
                ->whereIn('status', ['pending', 'failed'])
                ->get();

            if ($this->entity === 'CIEV') {
                $token = config('apidian.token');
                $entity_document = 890926617;
            } else {
                $token = config('apidian.token_goja');
                $entity_document = 900349726;
            }
            $url = 'http://192.168.1.44:8400/api';
            set_time_limit(0);

            $client = new Client(['base_uri' => $url]);
            $headers = [
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ];

            foreach ($list as $item) {
                $this->current_id = $item->id;
                DB::beginTransaction();
                try {
                    switch ($item->type_operation) {
                        case 'destroy':
                        case 'adjust':
                            $this->response_api = $client->request('POST', "{$url}/ubl2.1/payroll-adjust-note", [
                                'headers' => $headers,
                                'json' => $item->payload,
                            ]);
                            break;
                        case 'payroll':
                            $this->response_api = $client->request('POST', "{$url}/ubl2.1/payroll", [
                                'headers' => $headers,
                                'json' => $item->payload,
                            ]);
                            break;
                    }

                    $resp = $this->response_api->getBody()->getContents();
                    $resp = is_string($resp)
                        ? json_decode($resp)
                        : $resp;

                    PayrollLog::create([
                        'consecutive' => $item->consecutive,
                        'status' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid,
                        'statusCode' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusCode,
                        'ErrorMessage' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string ?? '',
                        'cune' => $resp->cune,
                        'send_by' => $this->user_id,
                        'entity' => $item->entity,
                    ]);

                    if ($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid === 'true' || str_contains($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string, 'procesado anteriormente')) {
                        $doc = PayrollDocument::find($this->current_id);
                        $doc->status = 'success';
                        $doc->save();

                        if (str_contains($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string, 'procesado anteriormente')) {
                            $document = \App\Models\Dian\PayrollDocument::where('identification_number', '=', $entity_document)
                                ->where('consecutive', '=', $item->consecutive)
                                ->first();

                            $document->update([
                                'state_document_id' => 1,
                                'cune' => $resp->cune,
                            ]);
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    PayrollLog::create([
                        'consecutive' => $this->current_id,
                        'status' => 'FAILED',
                        'statusCode' => $e->getCode(),
                        'ErrorMessage' => $e->getMessage(),
                        'cune' => 'NA',
                        'send_by' => $this->user_id,
                        'entity' => $item->entity,
                    ]);

                    $doc = PayrollDocument::find($this->current_id);
                    $doc->status = 'failed';
                    $doc->save();
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
