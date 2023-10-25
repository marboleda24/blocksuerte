<?php

namespace App\Console\Commands;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class CheckEventDian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-event-dian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $query = DB::table('supplier_purchases')
            ->select('id', DB::raw("JSON_VALUE(document_information, '$.UUID') AS CUFE"),
                DB::raw("DATEDIFF(DAY, JSON_VALUE(document_information, '$.IssueDate'), GETDATE()) AS days"),
                'entity', 'dian_state', 'created_at')
            ->whereNotIn('dian_state', ['02', '031', '033', '034'])
            ->get();

        $companies = $query->groupBy('entity');

        foreach ($companies as $company) {
            foreach ($company as $item) {
                if ($item->days === null || $item->days > 5){
                    $this->sendEvent($item->entity, $item->CUFE, $item->dian_state, $item->id);
                }
            }
        }
    }

    /**
     * @param $entity
     * @param $cufe
     * @param $last_event
     * @param $id
     * @return object
     * @throws GuzzleException
     * @throws Throwable
     */
    protected function sendEvent($entity, $cufe, $last_event, $id){
        DB::beginTransaction();
        try {
            $event = DB::connection('API_DIAN')
                ->table('events')
                ->where('code', '=', $event_code)
                ->first();

            $params = [
                'event_id' => $event->id,
                'receiver' => [
                    'identification_number' => $supplier_purchase->supplier['CompanyID'],
                    'dv' => calculateDV($supplier_purchase->supplier['CompanyID']),
                    'name' => $supplier_purchase->supplier['Name'],
                    'tax_id' => 1,
                ],
                'document_reference' => [
                    'number' => str_replace($supplier_purchase->document_information['Prefix'], '', $supplier_purchase->document_information['ID']),
                    'prefix' => $supplier_purchase->document_information['Prefix'],
                    'uuid' => $supplier_purchase->document_information['UUID'],
                    'type_document_id' => 1,
                ],
            ];

            if ($entity === 'CIEV'){
                $params['sender'] = [
                    'identification_number' => 890926617,
                    'dv' => 8,
                    'name' => 'CI ESTRADA VELASQUEZ Y CIA SAS',
                    'tax_id' => 1,
                ];
                $token = config('apidian.token');
            }else {
                $params['sender'] = [
                    'identification_number' => 900349726,
                    'dv' => 2,
                    'name' => 'PLASTICOS GOJA S.A.S',
                    'tax_id' => 1,
                ];
                $token = config('apidian.token_goja');
            }

            $url = config('apidian.url');
            $client = new Client(['base_uri' => $url]);

            $headers = [
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ];

            $response = $client->request('POST', "{$url}/ubl2.1/send-event", [
                'headers' => $headers,
                'json' => $params,
            ]);

            $result = json_decode($response->getBody()->getContents())->ResponseDian->Envelope->Body->SendEventUpdateStatusResponse->SendEventUpdateStatusResult;

            if ($result->IsValid === 'true' || $result->IsValid === 'false' && str_contains($result->ErrorMessage->string, 'Regla: 90') || $result->IsValid === 'false' && str_contains($result->ErrorMessage->string, 'Regla: DC24c')) {
                $supplier_purchase->dian_state = $event_code;
                $supplier_purchase->dian_state === '032'
                    ? $supplier_purchase->received_by = 1
                    : $supplier_purchase->accepted_by = 1;
                $supplier_purchase->save();

                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
