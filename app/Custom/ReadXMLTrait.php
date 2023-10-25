<?php

namespace App\Custom;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;
use SimpleXMLElement;

trait ReadXMLTrait
{
    public $original_document = null;

    public $response = null;

    public $xml = null;

    public $document = [
        'ApplicationResponse' => [],
        'Supplier' => [],
        'Customer' => [],
        'PaymentMeans' => [],
        'PaymentTerms' => [],
        'AllowanceCharge' => [],
        'LegalMonetaryTotal' => [],
        'TaxTotal' => [],
        'WithholdingTaxTotals' => [],
        'Items' => [],
        'DocumentInformation' => [],
    ];

    /**
     * @param $xmlPath
     * @return array
     *
     * @throws Exception
     */
    protected function readDocument($xmlPath): array
    {
        $this->init($xmlPath);
        $this->statusDocument();
        $this->documentData();
        $this->Supplier();
        $this->Customer();
        $this->PaymentMeans();
        $this->PaymentTerms();
        $this->AllowanceCharge();
        $this->LegalMonetaryTotal();
        $this->TaxTotal();
        $this->WithholdingTaxTotal();
        $this->InvoiceLines();
        $this->QRCode();

        return $this->document;
    }

    /**
     * @throws Exception
     */
    protected function init($xmlPath)
    {
        $this->xml = file_get_contents($xmlPath);
        $document = new SimpleXMLElement($this->xml);
        $this->original_document = $document->xpath('//*[local-name()="AttachedDocument"]/*[name()="cac:Attachment"]/*[name()="cac:ExternalReference"]/*[name()="cbc:Description"]')[0];
        $this->response = $document->xpath('//*[local-name()="AttachedDocument"]/*[name()="cac:ParentDocumentLineReference"]/*[name()="cac:DocumentReference"]/*[name()="cac:Attachment"]/*[name()="cac:ExternalReference"]/*[name()="cbc:Description"]')[0];

        $xml = new SimpleXMLElement($this->original_document);

        if (! $xml->xpath('//*[local-name()="Invoice"]')) {
            throw new Exception('El documento no es una factura electronica valida', '401');
        }
    }

    /**
     * @throws Exception
     */
    protected function statusDocument()
    {
        $xml = new SimpleXMLElement($this->response);

        $this->document['ApplicationResponse'] = [
            'UUID' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cbc:UUID"]')[0],
            'UBLVersionID' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cbc:UBLVersionID"]')[0],
            'ProfileID' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cbc:ProfileID"]')[0],
            'IssueDate' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cbc:IssueDate"]')[0],
            'IssueTime' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cbc:IssueTime"]')[0],
            'ResponseCode' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cac:DocumentResponse"]/*[name()="cac:Response"]/*[name()="cbc:ResponseCode"]')[0],
            'Description' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cac:DocumentResponse"]/*[name()="cac:Response"]/*[name()="cbc:Description"]')[0],
            'Message' => (string) $xml->xpath('//*[local-name()="ApplicationResponse"]/*[name()="cac:DocumentResponse"]/*[name()="cac:LineResponse"]/*[name()="cac:Response"]/*[name()="cbc:Description"]')[1],
        ];
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function documentData()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $this->document['DocumentInformation'] = [
            'ID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cbc:ID"]')[0],
            'UUID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cbc:UUID"]')[0],
            'Prefix' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="ext:UBLExtensions"]/*[name()="ext:UBLExtension"]/*[name()="ext:ExtensionContent"]/*[name()="sts:DianExtensions"]/*[name()="sts:InvoiceControl"]/*[name()="sts:AuthorizedInvoices"]/*[name()="sts:Prefix"]')[0],
            'InvoiceTypeCode' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cbc:InvoiceTypeCode"]')[0],
            'DocumentCurrencyCode' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cbc:DocumentCurrencyCode"]')[0],
            'LineCountNumeric' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cbc:LineCountNumeric"]')[0],
        ];
    }

    /**
     * @throws Exception
     */
    protected function Supplier()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $this->document['Supplier'] = [
            'Name' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyLegalEntity"]/*[name()="cbc:RegistrationName"]')[0],
            'CompanyID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyLegalEntity"]/*[name()="cbc:CompanyID"]')[0],
            'ZipCode' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyTaxScheme"]/*[name()="cac:RegistrationAddress"]/*[name()="cbc:ID"]')[0],
            'City' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyTaxScheme"]/*[name()="cac:RegistrationAddress"]/*[name()="cbc:CityName"]')[0],
            'Department' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyTaxScheme"]/*[name()="cac:RegistrationAddress"]/*[name()="cbc:CountrySubentity"]')[0],
            'Country' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyTaxScheme"]/*[name()="cac:RegistrationAddress"]/*[name()="cac:Country"]/*[name()="cbc:Name"]')[0],
            'Address' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:PartyTaxScheme"]/*[name()="cac:RegistrationAddress"]/*[name()="cac:AddressLine"]/*[name()="cbc:Line"]')[0],
            'Telephone' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:Telephone"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:Telephone"]')[0] : '',
            'ElectronicMail' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:ElectronicMail"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingSupplierParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:ElectronicMail"]')[0] : '',
        ];
    }

    /**
     * @throws Exception
     */
    protected function Customer()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $company_id = (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PartyLegalEntity"]/*[name()="cbc:CompanyID"]')[0];

        if ($xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PartyLegalEntity"]') && $company_id !== '890926617') {
            $this->document['Customer'] = [
                'Name' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PartyLegalEntity"]/*[name()="cbc:RegistrationName"]')[0],
                'CompanyID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PartyLegalEntity"]/*[name()="cbc:CompanyID"]')[0],
                'ZipCode' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PhysicalLocation"]/*[name()="cac:Address"]/*[name()="cbc:ID"]')[0],
                'City' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PhysicalLocation"]/*[name()="cac:Address"]/*[name()="cbc:CityName"]')[0],
                'Department' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PhysicalLocation"]/*[name()="cac:Address"]/*[name()="cbc:CountrySubentity"]')[0],
                'Country' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PhysicalLocation"]/*[name()="cac:Address"]/*[name()="cac:Country"]/*[name()="cbc:Name"]')[0],
                'Address' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:PhysicalLocation"]/*[name()="cac:Address"]/*[name()="cac:AddressLine"]/*[name()="cbc:Line"]')[0],
                'Telephone' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:Telephone"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:Telephone"]')[0] : '',
                'ElectronicMail' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:ElectronicMail"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AccountingCustomerParty"]/*[name()="cac:Party"]/*[name()="cac:Contact"]/*[name()="cbc:ElectronicMail"]')[0] : '',
            ];
        } else {
            $this->document['Customer'] = [
                'Name' => 'COMERCIALIZADORA INTERNACIONAL ESTRADA VELASQUEZ Y CIA S.A.S',
                'CompanyID' => '890926617',
                'ZipCode' => '05001',
                'City' => 'Medellin',
                'Department' => 'Antioquia',
                'Country' => 'Colombia',
                'Address' => "CARRERA 55 N\u00b0 29 C 14",
                'Telephone' => '2656665',
                'ElectronicMail' => 'compras.fe@estradavelasquez.com;sistemas@estradavelasquez.com',
            ];
        }
    }

    /**
     * @throws Exception
     */
    protected function PaymentMeans()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $this->document['PaymentMeans'] = [
            'ID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentMeans"]/*[name()="cbc:ID"]')[0],
            'PaymentMeansCode' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentMeans"]/*[name()="cbc:PaymentMeansCode"]')[0],
            'PaymentDueDate' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentMeans"]/*[name()="cbc:PaymentDueDate"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentMeans"]/*[name()="cbc:PaymentDueDate"]')[0] : '',
            'PaymentID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentMeans"]/*[name()="cbc:PaymentMeansCode"]')[0],
        ];
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function PaymentTerms()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $this->document['PaymentTerms'] = [
            'ReferenceEventCode' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentTerms"]/*[name()="cbc:ReferenceEventCode"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentTerms"]/*[name()="cbc:ReferenceEventCode"]')[0] : '',
            'DurationMeasure' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentTerms"]/*[name()="cac:SettlementPeriod"]/*[name()="cbc:DurationMeasure"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:PaymentTerms"]/*[name()="cac:SettlementPeriod"]/*[name()="cbc:DurationMeasure"]')[0] : '',
        ];
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function AllowanceCharge()
    {
        $xml = new SimpleXMLElement($this->original_document);

        if ($xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]')) {
            $this->document['AllowanceCharge'] = [
                'ID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:ID"]')[0],
                'ChargeIndicator' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:ChargeIndicator"]')[0],
                'AllowanceChargeReasonCode' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:AllowanceChargeReasonCode"]')[0],
                'AllowanceChargeReason' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:AllowanceChargeReason"]') ? (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:AllowanceChargeReason"]')[0] : '',
                'MultiplierFactorNumeric' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:MultiplierFactorNumeric"]')[0],
                'Amount' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:Amount"]')[0],
                'BaseAmount' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:AllowanceCharge"]/*[name()="cbc:BaseAmount"]')[0],
            ];
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function LegalMonetaryTotal()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $this->document['LegalMonetaryTotal'] = [
            'LineExtensionAmount' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:LegalMonetaryTotal"]/*[name()="cbc:LineExtensionAmount"]')[0],
            'TaxExclusiveAmount' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:LegalMonetaryTotal"]/*[name()="cbc:TaxExclusiveAmount"]')[0],
            'TaxInclusiveAmount' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:LegalMonetaryTotal"]/*[name()="cbc:TaxInclusiveAmount"]')[0],
            'AllowanceTotalAmount' => $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:LegalMonetaryTotal"]/*[name()="cbc:AllowanceTotalAmount"]') ? (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:LegalMonetaryTotal"]/*[name()="cbc:AllowanceTotalAmount"]')[0] : '',
            'PayableAmount' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:LegalMonetaryTotal"]/*[name()="cbc:PayableAmount"]')[0],
        ];
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function TaxTotal()
    {
        $xml = new SimpleXMLElement($this->original_document);

        if ($xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:TaxTotal"]')) {
            $this->document['TaxTotal'] = [
                'ID' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:TaxTotal"]/*[name()="cac:TaxSubtotal"]/*[name()="cac:TaxCategory"]/*[name()="cac:TaxScheme"]/*[name()="cbc:ID"]')[0],
                'Name' => (string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:TaxTotal"]/*[name()="cac:TaxSubtotal"]/*[name()="cac:TaxCategory"]/*[name()="cac:TaxScheme"]/*[name()="cbc:Name"]')[0],
                'TaxAmount' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:TaxTotal"]/*[name()="cbc:TaxAmount"]')[0],
                'TaxableAmount' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:TaxTotal"]/*[name()="cac:TaxSubtotal"]/*[name()="cbc:TaxableAmount"]')[0],
                'Percent' => (float) $xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:TaxTotal"]/*[name()="cac:TaxSubtotal"]/*[name()="cac:TaxCategory"]/*[name()="cbc:Percent"]')[0],
            ];
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function WithholdingTaxTotal()
    {
        $xml = new SimpleXMLElement($this->original_document);

        if ($xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:WithholdingTaxTotal"]')) {
            foreach ($xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:WithholdingTaxTotal"]') as $row) {
                $this->document['WithholdingTaxTotals'][] = [
                    'ID' => (string) $row->xpath('cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID')[0],
                    'Name' => (string) $row->xpath('cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name')[0],
                    'TaxAmount' => (float) $row->xpath('cbc:TaxAmount')[0],
                    'TaxableAmount' => (float) $row->xpath('cac:TaxSubtotal/cbc:TaxableAmount')[0],
                    'Percent' => (float) $row->xpath('cac:TaxSubtotal/cac:TaxCategory/cbc:Percent')[0],
                ];
            }
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function InvoiceLines()
    {
        $xml = new SimpleXMLElement($this->original_document);

        foreach ($xml->xpath('//*[local-name()="Invoice"]/*[name()="cac:InvoiceLine"]') as $row) {
            $item = [
                'Code' => $row->xpath('cac:Item/cac:StandardItemIdentification/cbc:ID') ? (string) $row->xpath('cac:Item/cac:StandardItemIdentification/cbc:ID')[0] : (string) $row->xpath('cbc:ID')[0],
                'Description' => (string) $row->xpath('cac:Item/cbc:Description')[0],
                'Quantity' => (float) $row->xpath('cbc:InvoicedQuantity')[0],
                'UnitPrice' => (float) $row->xpath('cac:Price/cbc:PriceAmount')[0],
                'Price' => (float) $row->xpath('cbc:LineExtensionAmount')[0],
            ];

            if ($row->xpath('cac:TaxTotal')) {
                $item['TaxTotal'] = [
                    'ID' => (string) $row->xpath('cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID')[0],
                    'Name' => (string) $row->xpath('cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name')[0],
                    'TaxAmount' => (float) $row->xpath('cac:TaxTotal/cac:TaxSubtotal/cbc:TaxAmount')[0],
                    'TaxableAmount' => (float) $row->xpath('cac:TaxTotal/cac:TaxSubtotal/cbc:TaxableAmount')[0],
                    'Percent' => (float) $row->xpath('cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:Percent')[0],
                ];
            }

            if ($row->xpath('cac:AllowanceCharge')) {
                $item['AllowanceCharge'] = [
                    'ID' => $row->xpath('cac:AllowanceCharge/cbc:ID') ? (string) $row->xpath('cac:AllowanceCharge/cbc:ID')[0] : '',
                    'ChargeIndicator' => (string) $row->xpath('cac:AllowanceCharge/cbc:ChargeIndicator')[0],
                    'AllowanceChargeReason' => $row->xpath('cac:AllowanceCharge/cbc:AllowanceChargeReason') ? (string) $row->xpath('cac:AllowanceCharge/cbc:AllowanceChargeReason')[0] : '',
                    'MultiplierFactorNumeric' => (float) $row->xpath('cac:AllowanceCharge/cbc:MultiplierFactorNumeric')[0],
                    'Amount' => (float) $row->xpath('cac:AllowanceCharge/cbc:Amount')[0],
                    'BaseAmount' => (float) $row->xpath('cac:AllowanceCharge/cbc:Amount')[0],
                ];
            }

            $this->document['Items'][] = $item;
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function QRCode()
    {
        $xml = new SimpleXMLElement($this->original_document);

        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString((string) $xml->xpath('//*[local-name()="Invoice"]/*[name()="ext:UBLExtensions"]/*[name()="ext:UBLExtension"]/*[name()="ext:ExtensionContent"]/*[name()="sts:DianExtensions"]/*[name()="sts:QRCode"]')[0]);

        $this->document['QRCode'] = trim(substr($svg, strpos($svg, "\n") + 1));
    }
}
