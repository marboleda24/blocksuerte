<?php

namespace App\Custom;

use DOMDocument;

trait ReadXML
{
    public $original_document = null;

    public $response = null;

    public $xml = null;

    public $document = [
        'applicationResponse' => [],
        'senderParty' => [],
        'receiverParty' => [],
        'attachedDocument' => [],
        'documentInformation' => [],
        'paymentMeans' => [],
        'paymentTerms' => [],
        'paymentExchangeRate' => [],
        'taxTotal' => [],
        'legalMonetaryTotal' => [],
        'invoiceLines' => [],
        'allowanceCharge' => [],
        'withholdingTaxTotals' => [],
    ];

    /**
     * @param $xml
     * @return array
     */
    protected function readXMLv2($xml): array
    {
        $xml = file_get_contents($xml);
        $responseDIAN = new DOMDocument;
        $responseDIAN->loadXML($xml);

        $this->attachedDocument($responseDIAN);
        $this->senderParty($responseDIAN->documentElement->getElementsByTagName('SenderParty')->item(0));
        $this->receiverParty($responseDIAN->documentElement->getElementsByTagName('ReceiverParty')->item(0));

        $this->original_document = $responseDIAN->documentElement
            ->getElementsByTagName('Attachment')->item(0)
            ->getElementsByTagName('ExternalReference')->item(0)
            ->getElementsByTagName('Description')->item(0)->nodeValue;

        $application_response = $responseDIAN->documentElement->getElementsByTagName('ParentDocumentLineReference')->item(0)
            ->getElementsByTagName('DocumentReference')->item(0)
            ->getElementsByTagName('Attachment')->item(0)
            ->getElementsByTagName('ExternalReference')->item(0)
            ->getElementsByTagName('Description')->item(0)->nodeValue;

        $this->loadOriginalDocument();
        $this->applicationResponse($application_response);

        return $this->document;
    }

    /**
     * @param $application_response
     * @return void
     */
    protected function applicationResponse($application_response): void
    {
        $applicationResponse = new DOMDocument;
        $applicationResponse->loadXML($application_response);

        $this->document['applicationResponse'] = [
            'UUID' => $applicationResponse->documentElement->getElementsByTagName('UUID')->item(0)->nodeValue,
            'UBLVersionID' => $applicationResponse->documentElement->getElementsByTagName('UBLVersionID')->item(0)->nodeValue,
            'ProfileID' => $applicationResponse->documentElement->getElementsByTagName('ProfileID')->item(0)->nodeValue,
            'IssueDate' => $applicationResponse->documentElement->getElementsByTagName('IssueDate')->item(0)->nodeValue,
            'IssueTime' => $applicationResponse->documentElement->getElementsByTagName('IssueTime')->item(0)->nodeValue,
            'ResponseCode' => $applicationResponse->documentElement->getElementsByTagName('ResponseCode')->item(0)->nodeValue,
            'Description' => $applicationResponse->documentElement->getElementsByTagName('Description')->item(0)->nodeValue,
            'Message' => $applicationResponse->documentElement->getElementsByTagName('DocumentResponse')->item(0)->getElementsByTagName('Description')->item(0)->nodeValue,
        ];
    }

    /**
     * @param $document
     * @return void
     */
    protected function attachedDocument($document): void
    {
        $this->document['attachedDocument'] = [
            'UUID' => trim(preg_replace('/\s\s+/', ' ', $document->documentElement->getElementsByTagName('UUID')->item(0)->nodeValue)),
            'UBLVersionID' => $document->documentElement->getElementsByTagName('UBLVersionID')->item(0)->nodeValue,
            'CustomizationID' => $document->documentElement->getElementsByTagName('CustomizationID')->item(0)->nodeValue,
            'ProfileID' => $document->documentElement->getElementsByTagName('ProfileID')->item(0)->nodeValue,
            'ID' => $document->documentElement->getElementsByTagName('ID')->item(0)->nodeValue,
            'IssueDate' => $document->documentElement->getElementsByTagName('IssueDate')->item(0)->nodeValue,
            'IssueTime' => $document->documentElement->getElementsByTagName('IssueTime')->item(0)->nodeValue,
            'DocumentTypeCode' => $document->documentElement->getElementsByTagName('DocumentTypeCode')->item(0)->nodeValue ?? '',
            'ParentDocumentID' => $document->documentElement->getElementsByTagName('ParentDocumentID')->item(0)->nodeValue,
        ];
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function senderParty($xpath): void
    {
        $this->document['senderParty'] = [
            'Name' => $xpath->getElementsByTagName('PartyTaxScheme')->item(0)->getElementsByTagName('RegistrationName')->item(0)->nodeValue,
            'CompanyID' => trim(preg_replace('/\s\s+/', ' ', $xpath->getElementsByTagName('PartyTaxScheme')->item(0)->getElementsByTagName('CompanyID')->item(0)->nodeValue)),
        ];
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function receiverParty($xpath): void
    {
        $this->document['receiverParty'] = [
            'Name' => $xpath->getElementsByTagName('PartyTaxScheme')->item(0)->getElementsByTagName('RegistrationName')->item(0)->nodeValue,
            'CompanyID' => trim(preg_replace('/\s\s+/', ' ', $xpath->getElementsByTagName('PartyTaxScheme')->item(0)->getElementsByTagName('CompanyID')->item(0)->nodeValue)),
        ];
    }

    /**
     * @return void
     */
    protected function loadOriginalDocument(): void
    {
        $xml = new DOMDocument;
        $xml->loadXML($this->original_document);

        $this->documentInformation($xml);
        $this->paymentMeans($xml->documentElement->getElementsByTagName('PaymentMeans')->item(0));
        $this->paymentTerms($xml->documentElement->getElementsByTagName('PaymentTerms')->item(0));
        $this->paymentExchangeRate($xml->documentElement->getElementsByTagName('PaymentExchangeRate')->item(0));
        $this->taxTotal($xml->documentElement->getElementsByTagName('TaxTotal')->item(0));
        $this->legalMonetaryTotal($xml->documentElement->getElementsByTagName('LegalMonetaryTotal')->item(0));
        $this->invoiceLines($xml->documentElement->getElementsByTagName('InvoiceLine'));
        $this->allowanceCharge($xml->documentElement->getElementsByTagName('AllowanceCharge')->item(0));
        $this->withholdingTaxTotals($xml->documentElement->getElementsByTagName('WithholdingTaxTotal'));
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function documentInformation($xpath): void
    {
        $this->document['documentInformation'] = [
            'UUID' => $xpath->documentElement->getElementsByTagName('UUID')->item(0)->nodeValue,
            'UBLVersionID' => $xpath->documentElement->getElementsByTagName('UBLVersionID')->item(0)->nodeValue,
            'CustomizationID' => $xpath->documentElement->getElementsByTagName('CustomizationID')->item(0)->nodeValue,
            'ProfileID' => $xpath->documentElement->getElementsByTagName('ProfileID')->item(0)->nodeValue,
            'Prefix' => $xpath->documentElement->getElementsByTagName('Prefix')->item(0)->nodeValue ?? '',
            'ID' => $xpath->documentElement->getElementsByTagName('ID')->item(0)->nodeValue,
            'IssueDate' => $xpath->documentElement->getElementsByTagName('IssueDate')->item(0)->nodeValue,
            'IssueTime' => $xpath->documentElement->getElementsByTagName('IssueTime')->item(0)->nodeValue,
            'DueDate' => $xpath->documentElement->getElementsByTagName('DueDate')->item(0)->nodeValue ?? null,
            'DocumentCurrencyCode' => $xpath->documentElement->getElementsByTagName('DueDate')->item(0)->nodeValue ?? null,
        ];
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function paymentMeans($xpath): void
    {
        $this->document['paymentMeans'] = [
            'ID' => $xpath->getElementsByTagName('ID')->item(0)->nodeValue,
            'PaymentMeansCode' => $xpath->getElementsByTagName('PaymentMeansCode')->item(0)->nodeValue,
            'PaymentDueDate' => $xpath->getElementsByTagName('PaymentDueDate')->item(0)->nodeValue ?? '',
            'PaymentID' => $xpath->getElementsByTagName('PaymentID')->item(0)->nodeValue ?? '',
        ];
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function paymentTerms($xpath): void
    {
        if ($xpath) {
            $this->document['paymentTerms'] = [
                'ID' => $xpath->getElementsByTagName('ID')->item(0)->nodeValue ?? null,
                'PaymentMeansID' => $xpath->getElementsByTagName('PaymentMeansID')->item(0)->nodeValue ?? null,
                'Note' => $xpath->getElementsByTagName('Note')->item(0)->nodeValue ?? null,
                'Amount' => $xpath->getElementsByTagName('Amount')->item(0)->nodeValue ?? null,
                'PaymentDueDate' => $xpath->getElementsByTagName('PaymentDueDate')->item(0)->nodeValue ?? null,
            ];
        }
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function paymentExchangeRate($xpath): void
    {
        if ($xpath) {
            $this->document['paymentExchangeRate'] = [
                'SourceCurrencyCode' => $xpath->getElementsByTagName('SourceCurrencyCode')->item(0)->nodeValue,
                'SourceCurrencyBaseRate' => $xpath->getElementsByTagName('SourceCurrencyBaseRate')->item(0)->nodeValue ?? '',
                'TargetCurrencyCode' => $xpath->getElementsByTagName('TargetCurrencyCode')->item(0)->nodeValue ?? '',
                'TargetCurrencyBaseRate' => $xpath->getElementsByTagName('TargetCurrencyBaseRate')->item(0)->nodeValue ?? '',
                'CalculationRate' => $xpath->getElementsByTagName('CalculationRate')->item(0)->nodeValue ?? '',
                'Date' => $xpath->getElementsByTagName('Date')->item(0)->nodeValue ?? '',
            ];
        }
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function taxTotal($xpath): void
    {
        if ($xpath) {
            $this->document['taxTotal'] = [
                'TaxAmount' => $xpath->getElementsByTagName('TaxAmount')->item(0)->nodeValue,
                'RoundingAmount' => $xpath->getElementsByTagName('RoundingAmount')->item(0)->nodeValue ?? 0,
                'TaxEvidenceIndicator' => $xpath->getElementsByTagName('TaxEvidenceIndicator')->item(0)->nodeValue ?? '',
                'TaxableAmount' => $xpath->getElementsByTagName('TaxSubtotal')->item(0)->getElementsByTagName('TaxableAmount')->item(0)->nodeValue,
                'Percent' => $xpath->getElementsByTagName('TaxSubtotal')->item(0)->getElementsByTagName('TaxCategory')->item(0)->getElementsByTagName('Percent')->item(0)->nodeValue,
                'ID' => $xpath->getElementsByTagName('TaxSubtotal')->item(0)->getElementsByTagName('TaxCategory')->item(0)->getElementsByTagName('TaxScheme')->item(0)->getElementsByTagName('ID')->item(0)->nodeValue,
                'Name' => $xpath->getElementsByTagName('TaxSubtotal')->item(0)->getElementsByTagName('TaxCategory')->item(0)->getElementsByTagName('TaxScheme')->item(0)->getElementsByTagName('Name')->item(0)->nodeValue,
            ];
        }
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function legalMonetaryTotal($xpath): void
    {
        $this->document['legalMonetaryTotal'] = [
            'LineExtensionAmount' => $xpath->getElementsByTagName('LineExtensionAmount')->item(0)->nodeValue,
            'TaxExclusiveAmount' => $xpath->getElementsByTagName('TaxExclusiveAmount')->item(0)->nodeValue,
            'TaxInclusiveAmount' => $xpath->getElementsByTagName('TaxInclusiveAmount')->item(0)->nodeValue,
            'AllowanceTotalAmount' => $xpath->getElementsByTagName('AllowanceTotalAmount')->item(0)->nodeValue ?? 0,
            'ChargeTotalAmount' => $xpath->getElementsByTagName('ChargeTotalAmount')->item(0)->nodeValue ?? 0,
            'PrepaidAmount' => $xpath->getElementsByTagName('PrepaidAmount')->item(0)->nodeValue ?? 0,
            'PayableRoundingAmount' => $xpath->getElementsByTagName('PayableRoundingAmount')->item(0)->nodeValue ?? 0,
            'PayableAmount' => $xpath->getElementsByTagName('PayableAmount')->item(0)->nodeValue,
        ];
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function invoiceLines($xpath): void
    {
        foreach ($xpath as $key => $item) {
            $this->document['invoiceLines'][$key] = [
                'ID' => $item->getElementsByTagName('ID')->item(0)->nodeValue,
                'InvoicedQuantity' => $item->getElementsByTagName('InvoicedQuantity')->item(0)->nodeValue,
                'LineExtensionAmount' => $item->getElementsByTagName('LineExtensionAmount')->item(0)->nodeValue,
                'Description' => $item->getElementsByTagName('Item')->item(0)->getElementsByTagName('Description')->item(0)->nodeValue,
                'PriceAmount' => $item->getElementsByTagName('Price')->item(0)->getElementsByTagName('PriceAmount')->item(0)->nodeValue,
                'BaseQuantity' => $item->getElementsByTagName('Price')->item(0)->getElementsByTagName('BaseQuantity')->item(0)->nodeValue,
            ];

            if ($item->getElementsByTagName('TaxTotal')->item(0)) {
                $this->document['invoiceLines'][$key]['FreeOfChargeIndicator'] = $item->getElementsByTagName('FreeOfChargeIndicator')->item(0)->nodeValue ?? '';
                $this->document['invoiceLines'][$key]['TaxableAmount'] = $item->getElementsByTagName('TaxTotal')->item(0)->getElementsByTagName('TaxableAmount')->item(0)->nodeValue ?? '';
                $this->document['invoiceLines'][$key]['TaxAmount'] = $item->getElementsByTagName('TaxTotal')->item(0)->getElementsByTagName('TaxAmount')->item(0)->nodeValue;
                $this->document['invoiceLines'][$key]['TaxPercent'] = $item->getElementsByTagName('TaxTotal')->item(0)->getElementsByTagName('Percent')->item(0)->nodeValue;
                $this->document['invoiceLines'][$key]['TaxID'] = $item->getElementsByTagName('TaxTotal')->item(0)->getElementsByTagName('TaxScheme')->item(0)->getElementsByTagName('ID')->item(0)->nodeValue;
                $this->document['invoiceLines'][$key]['TaxName'] = $item->getElementsByTagName('TaxTotal')->item(0)->getElementsByTagName('TaxScheme')->item(0)->getElementsByTagName('Name')->item(0)->nodeValue;
            }
        }
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function allowanceCharge($xpath): void
    {
        if ($xpath) {
            $this->document['allowanceCharge'] = [
                'ID' => $xpath->getElementsByTagName('ID')->item(0)->nodeValue ?? '',
                'ChargeIndicator' => $xpath->getElementsByTagName('ChargeIndicator')->item(0)->nodeValue,
                'AllowanceChargeReason' => $xpath->getElementsByTagName('AllowanceChargeReason')->item(0)->nodeValue ?? null,
                'MultiplierFactorNumeric' => $xpath->getElementsByTagName('MultiplierFactorNumeric')->item(0)->nodeValue ?? null,
                'Amount' => $xpath->getElementsByTagName('Amount')->item(0)->nodeValue,
                'BaseAmount' => $xpath->getElementsByTagName('BaseAmount')->item(0)->nodeValue,
            ];
        }
    }

    /**
     * @param $xpath
     * @return void
     */
    protected function withholdingTaxTotals($xpath): void
    {
        if ($xpath) {
            foreach ($xpath as $item) {
                $this->document['withholdingTaxTotals'][] = [
                    'ID' => $item->getElementsByTagName('ID')->item(0)->nodeValue,
                    'Name' => $item->getElementsByTagName('Name')->item(0)->nodeValue,
                    'TaxAmount' => $item->getElementsByTagName('TaxAmount')->item(0)->nodeValue,
                    'TaxableAmount' => $item->getElementsByTagName('TaxableAmount')->item(0)->nodeValue,
                    'Percent' => $item->getElementsByTagName('Percent')->item(0)->nodeValue,
                ];
            }
        }
    }
}
