<?php

namespace App\Imports;

use DOMDocument;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use ubl21dian\Templates\SOAP\GetStatusEvent;

class InvoiceProviderVerificationImport implements ToCollection
{
    public $data;

    /**
     * @param  Collection  $collection
     * @return void
     *
     * @throws Exception
     */
    public function collection(Collection $collection): void
    {
        $i = 0;
        $result = [];

        foreach ($collection as $value) {
            if ($i > 0 && $value[0] === 'Factura electrónica') {
                $result[] = [
                    'Type' => $value[0],
                    'UUID' => $value[1],
                    'Prefix' => $value[3],
                    'Consecutive' => $value[2],
                    'IssueDate' => $value[4],
                    'ReceptionDate' => $value[5],
                    'EmisorDocument' => $value[6],
                    'EmisorName' => $value[7],
                    'IVA' => $value[10],
                    'ICA' => $value[11],
                    'IPC' => $value[12],
                    'Total' => $value[13],
                    'Status' => $this->eventStatus($value[1]),
                ];
            }
            $i++;
        }
        $this->data = $result;
    }

    /**
     * @param $trackId
     * @return array
     *
     * @throws Exception
     */
    public function eventStatus($trackId): array
    {
        $certificate = storage_path('app/certificates/8909266178.p12');

        $eventStatus = new GetStatusEvent($certificate, 'CIEV2011ev', 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc');
        $eventStatus->trackId = $trackId;

        if (! is_dir(storage_path('app/public/8909266178'))) {
            mkdir(storage_path('app/public/8909266178'));
        }

        $ar = new DOMDocument;

        $response_dian = $eventStatus->signToSend(storage_path('app/public/8909266178/ReqZIP-'.$trackId.'.xml'))
            ->getResponseToObject(storage_path('app/public/8909266178/RptaZIP-'.$trackId.'.xml'));

        if ($response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->IsValid == 'true') {
            $uuid = $response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->XmlDocumentKey;

            $app_response_xml = base64_decode($response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->XmlBase64Bytes);
            $ar->loadXML($app_response_xml);

            $validation_date = $ar->documentElement->getElementsByTagName('IssueDate')->item(0)->nodeValue;
            $validation_time = $ar->documentElement->getElementsByTagName('IssueTime')->item(0)->nodeValue;

            $events = $ar->documentElement->getElementsByTagName('DocumentResponse');

            $events_result = [];

            foreach ($events as $event) {
                $events_result[] = [
                    'ResponseCode' => $event->getElementsByTagName('Response')->item(0)->getElementsByTagName('ResponseCode')->item(0)->nodeValue,
                    'Description' => $event->getElementsByTagName('Response')->item(0)->getElementsByTagName('Description')->item(0)->nodeValue,
                    'EffectiveDate' => $event->getElementsByTagName('Response')->item(0)->getElementsByTagName('EffectiveDate')->item(0)->nodeValue,
                    'EffectiveTime' => $event->getElementsByTagName('Response')->item(0)->getElementsByTagName('EffectiveTime')->item(0)->nodeValue,
                    'ID' => $event->getElementsByTagName('DocumentReference')->item(0)->getElementsByTagName('ID')->item(0)->nodeValue,
                    'UUID' => $event->getElementsByTagName('DocumentReference')->item(0)->getElementsByTagName('UUID')->item(0)->nodeValue,
                ];
            }
        }

        return [
            'message' => 'Consulta generada con éxito',
            'ResponseDian' => [
                'ErrorMessage' => $response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->ErrorMessage,
                'IsValid' => $response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->IsValid,
                'StatusCode' => $response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->StatusCode,
                'StatusDescription' => $response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->StatusDescription,
                'StatusMessage' => $response_dian->Envelope->Body->GetStatusEventResponse->GetStatusEventResult->StatusMessage,
            ],
            'Result' => $events_result ?? [],
            'IssueDate' => $validation_date ?? '',
            'IssueTime' => $validation_time ?? '',
            'UUID' => $uuid ?? '',
        ];
    }
}
