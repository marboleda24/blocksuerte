<?php

namespace App\Traits;

use App\Models\Dian\ApiDocument;
use App\Models\Dian\DebitNote;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Faker\Provider\Company;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

trait ElectronicBillingTrait
{
    /**
     * @param $invoice
     * @param string $entity
     * @return string
     * @throws GuzzleException
     * @throws Throwable
     */
    protected function sendInvoiceDian($invoice, string $entity = 'CIEV'): string
    {
        if ($entity === 'GOJA'){
            $this->updateRetentionGoja($invoice);
        }

        $resolution = DB::connection('API_DIAN')
            ->table('resolutions')
            ->where('company_id', '=', $entity === 'CIEV' ? 3 : 4)
            ->where('type_document_id', '=', 1)
            ->first();

        $header = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
            ->where('NUMERO', '=', $invoice)
            ->first();

        $detail = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasDetalladas' : 'PG_V_FE_FacturasDetalladas')
            ->where('factura', '=', $invoice)
            ->get();

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('code', '=', $header->CODIGOCIUDAD)
            ->first();

        $params = [
            'number' => $invoice,
            'type_document_id' => 1,
            'date' => $header->FECHA,
            'time' => $header->HORA,
            'resolution_number' => $resolution->resolution,
            'prefix' => $resolution->prefix,
            'sendmail' => true,
            'email_cc_list' => trim($header->CORREOSCOPIA) == '' ? null : preg_split('/([,;])/', $header->CORREOSCOPIA),
            'notes' => $header->COMENTARIOS,
            'seller' => $header->NOMVENDEDOR,
            'ov' => $header->OV,
            'oc' => $header->OC,
            'customer' => [
                'identification_number' => $header->IDENTIFICACION,
                'dv' => $header->DIGITOVERIFICACION,
                'name' => $header->RAZONSOCIAL,
                'phone' => $header->TELEFONO,
                'address' => $header->DIRECCION,
                'email' => $header->CORREOFE,
                'merchant_registration' => '0000000-00',
                'type_document_identification_id' => $header->TIPODOCUMENTO == 31 ? 3 : 6,
                'type_organization_id' => $header->TIPOORGANIZACION,
                'municipality_id' => $city !== null ? $city->id : 1,
                'type_regime_id' => $header->REGIMENFISCAL == 49 ? 2 : 1,
            ],

            'order_reference' => [
                'id_order' => $header->OC,
                'issue_date_order' => $header->FECHA,
            ],

            'payment_form' => [
                'payment_form_id' => $header->DIAS > 0 ? 2 : 1,
                'payment_method_id' => 42,
                'payment_due_date' => $header->VENCIMIENTO,
                'duration_measure' => $header->DIAS,
            ],
            'with_holding_tax_total' => [
                [
                    'tax_id' => 6,
                    'tax_amount' => number_format($header->RTEFTE, 2, '.', ''),
                    'percent' => $header->TASA_RTEFTE ? number_format($header->TASA_RTEFTE, 2, '.', '') : '0.00',
                    'taxable_amount' => number_format($header->SUBTOTAL, 2, '.', ''),
                ],
                [
                    'tax_id' => 5,
                    'tax_amount' => number_format($header->RTEIVA, 2, '.', ''),
                    'percent' => $header->RTEIVA > 0 ? '15.00' : '0.00',
                    'taxable_amount' => number_format($header->IVA, 2, '.', ''),
                ],
            ],
            'invoice_lines' => [],
            'free_items' => [],
        ];

        if ($header->IDENTIFICACION == '3052' || $header->IDENTIFICACION == '3053' || $header->IDENTIFICACION == '3055') {
            $params['customer'] = [
                'identification_number' => 222222222222,
                'dv' => 7,
                'name' => 'CONSUMIDOR FINAL',
                'merchant_registration' => '0000000-00',
                'municipality_id' => $city !== null ? $city->id : 1,
            ];

            $params['notes'] = "CUANTÍAS MENORES PUNTO DE VENTA: {$header->NOMVENDEDOR} CÓDIGO: {$header->IDENTIFICACION}";
        }

        $totals = [
            'bruto' => 0,
            'discount' => 0,
            'tax' => 0,
        ];

        foreach ($detail as $item) {
            $notes = DB::connection($entity === 'CIEV' ? 'MAX': 'MAXPG')
                ->table($entity === 'CIEV' ? 'CIEV_V_NotasFacturas' : 'PG_V_NotasFacturas')
                ->where('Factura', '=', $invoice)
                ->where('Item', '=', $item->Item)
                ->where('OV', '=', $header->OV)
                ->get();

            if ($item->Precio == 0) {
                $params['free_items'][] = [
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'base_quantity' => $item->Cantidad,
                ];
            } else {
                $price = number_format($item->Precio, 2, '.', '');
                $quantity = number_format($item->Cantidad, 2, '.', '');
                $discount = number_format($item->Desc_Item, 2, '.', '');
                $total_item = number_format(($price * $quantity), 2, '.', '');
                $playable = number_format(($total_item - $discount), 2, '.', '');
                $tax = number_format(($playable * 0.19), 2, '.', '');

                $params['invoice_lines'][] = [
                    'unit_measure_id' => $item->UM == 94 ? 70 : ($item->UM == 'KLG' ? 767 : 1016),
                    'invoiced_quantity' => $quantity,
                    'line_extension_amount' => $total_item,
                    'free_of_charge_indicator' => $price == 0,
                    'allowance_charges' => [
                        [
                            'charge_indicator' => false,
                            'allowance_charge_reason' => 'DESCUENTO GENERAL',
                            'amount' => $discount,
                            'base_amount' => $total_item,
                        ],
                    ],
                    'tax_totals' => [
                        [
                            'tax_id' => 1,
                            'tax_amount' => $item->IVA > 0 ? $tax : '00.00',
                            'taxable_amount' => $playable,
                            'percent' => $item->IVA > 0 ? '19.00' : '00.00',
                        ],
                    ],
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'type_item_identification_id' => 4,
                    'price_amount' => $price,
                    'base_quantity' => $quantity,
                    'brand' => trim($item->Marca),
                    'art' => trim($item->ARTE),
                    'notes' => $notes,
                    'cpc' => trim($item->CodProdCliente),
                ];
                $totals['bruto'] += $price * $quantity;
                $totals['discount'] += $discount;
                if ($header->IVA > 0) {
                    $totals['tax'] += $tax;
                }
            }
        }

        $params['allowance_charges'] = [
            [
                'discount_id' => 1,
                'charge_indicator' => false,
                'allowance_charge_reason' => 'DESCUENTO GENERAL',
                'amount' => number_format($totals['discount'], 2, '.', ''),
                'base_amount' => number_format($totals['bruto'], 2, '.', ''),
            ],
        ];

        $params['tax_totals'] = [
            [
                'tax_id' => 1,
                'tax_amount' => $header->IVA > 0 ? number_format($totals['tax'], 2, '.', '') : '00.00',
                'percent' => $header->IVA > 0 ? '19.00' : '00.00',
                'taxable_amount' => number_format($totals['bruto'] - $totals['discount'], 2, '.', ''),
            ],
        ];

        $params['legal_monetary_totals'] = [
            'line_extension_amount' => number_format($totals['bruto'], 2, '.', ''),
            'tax_exclusive_amount' => number_format($totals['bruto'] - $totals['discount'], 2, '.', ''),
            'tax_inclusive_amount' => number_format($totals['bruto'] + $totals['tax'], 2, '.', ''),
            'allowance_total_amount' => number_format($totals['discount'], 2, '.', ''),
            'charge_total_amount' => '0.00',
            'payable_amount' => number_format(($totals['bruto'] - $totals['discount']) + $totals['tax'], 2, '.', ''),
        ];

        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('POST', "{$url}/ubl2.1/invoice/", [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $invoice
     * @param string $entity
     * @return string
     * @throws GuzzleException
     * @throws Throwable
     */
    protected function sendInvoiceDianUSD($invoice, string $entity = 'CIEV'): string
    {
        //$this->updateTariffPosition();

        $resolution = DB::connection('API_DIAN')
            ->table('resolutions')
            ->where( 'Company_id', '=', $entity === 'CIEV' ? 3:4)
            ->where('type_document_id', '=', 1)
            ->first();

        $header = DB::connection($entity ==='CIEV' ? 'MAX':'MAXPG')
            ->table($entity === 'CIEV' ?'CIEV_V_FE_FacturasTotalizadas_Dian': 'PG_V_FE_FacturasTotalizadas_Dian')
            ->where('NUMERO', '=', $invoice)
            ->first();

        $detail = DB::connection($entity ==='CIEV' ? 'MAX':'MAXPG')
            ->table($entity === 'CIEV' ?'CIEV_V_FE_FacturasDetalladas':'PG_V_FE_FacturasDetalladas')
            ->where('factura', '=', $invoice)
            ->get();

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('code', '=', $header->CODIGOCIUDADCOMPLETO)
            ->first();

        $department = DB::connection('API_DIAN')
            ->table('departments')
            ->where('id', '=', $city->department_id)
            ->first();

        $country = DB::connection('API_DIAN')
            ->table('countries')
            ->where('id', '=', $department->country_id)
            ->first();

        $params = [
            'number' => $invoice,
            'type_document_id' => 1,
            'idcurrency' => 149,
            'calculationrate' => intval($header->TASA),
            'calculationratedate' => $header->FECHA,
            'date' => $header->FECHA,
            'time' => $header->HORA,
            'resolution_number' => $resolution->resolution,
            'prefix' => $resolution->prefix,
            'sendmail' => true,
            'email_cc_list' => trim($header->CORREOSCOPIA) == '' ? null : preg_split('/([,;])/', $header->CORREOSCOPIA),
            'notes' => $header->COMENTARIOS,
            'seller' => $header->NOMVENDEDOR,
            'ov' => $header->OV,
            'oc' => $header->OC,
            'customer' => [
                'identification_number' => $header->IDENTIFICACION,
                'dv' => $header->DIGITOVERIFICACION,
                'name' => $header->RAZONSOCIAL,
                'phone' => $header->TELEFONO,
                'address' => $header->DIRECCION,
                'email' => $header->CORREOFE,
                'merchant_registration' => '0000000-00',
                'type_document_identification_id' => $header->TIPODOCUMENTO == 31 ? 3 : 6,
                'type_organization_id' => $header->TIPOORGANIZACION,
                'municipality_id' => $city->id,
                'country_id' => $country->id,
                'type_regime_id' => $header->REGIMENFISCAL == 49 ? 2 : 1,
            ],
            'payment_form' => [
                'payment_form_id' => $header->DIAS > 0 ? 2 : 1,
                'payment_method_id' => 42,
                'payment_due_date' => $header->VENCIMIENTO,
                'duration_measure' => $header->DIAS,
            ],
            'invoice_lines' => [],
            'free_items' => [],
        ];

        $totals = [
            'bruto' => 0,
            'tax' => 0,
        ];

        foreach ($detail as $item) {
            $notes = DB::connection('MAX')
                ->table('CIEV_V_NotasFacturas')
                ->where('Factura', '=', $invoice)
                ->where('Item', '=', $item->Item)
                ->where('OV', '=', $item->OV)
                ->get();

            if ($item->PrecioUSD > 0) {
                $price = number_format($item->PrecioUSD, 2, '.', '');
                $quantity = number_format($item->Cantidad, 2, '.', '');
                $total_item = number_format(($price * $quantity), 2, '.', '');
                $tax = number_format(($total_item * 0.19), 2, '.', '');

                $params['invoice_lines'][] = [
                    'unit_measure_id' => $item->UM == 94 ? 70 : ($item->UM == 'KLG' ? 767 : 1016),
                    'invoiced_quantity' => $quantity,
                    'line_extension_amount' => $total_item,
                    'free_of_charge_indicator' => $price == 0,
                    'tax_totals' => [
                        [
                            'tax_id' => 1,
                            'tax_amount' => $item->IVA > 0 ? $tax : '00.00',
                            'taxable_amount' => $total_item,
                            'percent' => $item->IVA > 0 ? '19.00' : '00.00',
                        ],
                    ],
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'type_item_identification_id' => 4,
                    'price_amount' => $price,
                    'base_quantity' => $quantity,
                    'brand' => trim($item->Marca),
                    'art' => trim($item->ARTE),
                    'notes' => $notes,
                    'cpc' => trim($item->CodProdCliente),
                ];
                $totals['bruto'] += $price * $quantity;

                if ($header->IVA > 0) {
                    $totals['tax'] += $tax;
                }
            }
        }

        $params['tax_totals'] = [
            [
                'tax_id' => 1,
                'tax_amount' => $header->IVA > 0 ? number_format($totals['tax'], 2, '.', '') : '00.00',
                'percent' => $header->IVA > 0 ? '19.00' : '00.00',
                'taxable_amount' => number_format($totals['bruto'], 2, '.', ''),
            ],
        ];

        $params['legal_monetary_totals'] = [
            'line_extension_amount' => number_format($totals['bruto'], 2, '.', ''),
            'tax_exclusive_amount' => number_format($totals['bruto'], 2, '.', ''),
            'tax_inclusive_amount' => number_format($totals['bruto'] + $totals['tax'], 2, '.', ''),
            'allowance_total_amount' => '0.00',
            'charge_total_amount' => '0.00',
            'payable_amount' => number_format(($totals['bruto'] + $totals['tax']), 2, '.', ''),
        ];

        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('POST', "{$url}/ubl2.1/invoice/", [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $document
     * @param string $entity
     * @return string
     *
     * @throws GuzzleException
     */
    protected function sendCreditNoteDian($document, string $entity = 'CIEV'): string
    {
        $resolution = DB::connection('API_DIAN')
            ->table('resolutions')
            ->where( 'company_id', '=', $entity === 'CIEV' ? 3 : 4 )
            ->where('type_document_id', '=', '4')
            ->first();

        $header = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
            ->where('NUMERO', '=', $document)
            ->first();

        $detail = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasDetalladas' : 'PG_V_FE_FacturasDetalladas' )
            ->where('factura', '=', $document)
            ->get();

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('code', '=', $header->CODIGOCIUDAD)
            ->first();

        if ($header->OC && strlen(trim($header->OC)) > 0){
            $invoice_reference = " \r\n  DOCUMENTO REFERENCIA: {$header->OC}";
        }else {
            $invoice_reference = null;
        }

        $params = [
            'number' => $document,
            'type_document_id' => 4,
            'date' => $header->FECHA,
            'time' => $header->HORA,
            'resolution_number' => $resolution->resolution,
            'prefix' => $resolution->prefix,
            'notes' => $header->COMENTARIOS . $invoice_reference,
            'sendmail' => true,
            'email_cc_list' => trim($header->CORREOSCOPIA) == '' ? null : preg_split('/([,;])/', $header->CORREOSCOPIA),
            'customer' => [
                'identification_number' => $header->IDENTIFICACION,
                'dv' => $header->DIGITOVERIFICACION,
                'name' => $header->RAZONSOCIAL,
                'phone' => $header->TELEFONO,
                'address' => $header->DIRECCION,
                'email' => $header->CORREOFE,
                'merchant_registration' => '0000000-00',
                'type_document_identification_id' => $header->TIPODOCUMENTO == 31 ? 6 : 3,
                'type_organization_id' => $header->TIPOORGANIZACION,
                'municipality_id' => $city->id,
                'type_regime_id' => $header->REGIMENFISCAL == 49 ? 2 : 1,
            ],
            'with_holding_tax_total' => [
                [
                    'tax_id' => 6,
                    'tax_amount' => number_format($header->RTEFTE, 2, '.', ''),
                    'percent' => $header->TASA_RTEFTE ? number_format($header->TASA_RTEFTE, 2, '.', '') : '0.00',
                    'taxable_amount' => number_format($header->SUBTOTAL, 2, '.', ''),
                ],
                [
                    'tax_id' => 5,
                    'tax_amount' => number_format($header->RTEIVA, 2, '.', ''),
                    'percent' => $header->RTEIVA > 0 ? '15.00' : '0.00',
                    'taxable_amount' => number_format($header->IVA, 2, '.', ''),
                ],
            ],

            'credit_note_lines' => [],
            'free_items' => [],
        ];
        $params['type_operation_id'] = 8;

        $totals = [
            'bruto' => 0,
            'discount' => 0,
            'tax' => 0,
        ];

        foreach ($detail as $item) {
            if ($item->Precio == 0) {
                $params['free_items'][] = [
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'base_quantity' => $item->Cantidad,
                ];
            } else {
                $price = number_format($item->Precio, 2, '.', '');
                $quantity = number_format($item->Cantidad, 2, '.', '');
                $discount = number_format($item->Desc_Item, 2, '.', '');
                $total_item = number_format(($price * $quantity), 2, '.', '');
                $playable = number_format(($total_item - $discount), 2, '.', '');
                $tax = number_format(($playable * 0.19), 2, '.', '');

                $params['credit_note_lines'][] = [
                    'unit_measure_id' => $item->UM == 94 ? 70 : ($item->UM == 'KLG' ? 767 : 1016),
                    'invoiced_quantity' => $quantity,
                    'line_extension_amount' => $total_item,
                    'free_of_charge_indicator' => $price == 0,
                    'allowance_charges' => [
                        [
                            'charge_indicator' => false,
                            'allowance_charge_reason' => 'DESCUENTO GENERAL',
                            'amount' => $discount,
                            'base_amount' => $total_item,
                        ],
                    ],
                    'tax_totals' => [
                        [
                            'tax_id' => 1,
                            'tax_amount' => $item->IVA > 0 ? $tax : 0,
                            'taxable_amount' => $playable,
                            'percent' => $item->IVA > 0 ? '19.00' : '00.00',
                        ],
                    ],
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'type_item_identification_id' => 4,
                    'price_amount' => $price,
                    'base_quantity' => $quantity,
                    'brand' => trim($item->Marca),
                    'art' => trim($item->ARTE),
                    'notes' => null,
                ];

                $totals['bruto'] += $price * $quantity;
                $totals['discount'] += $discount;
                if ($header->IVA > 0) {
                    $totals['tax'] += $tax;
                }
            }
        }

        $params['allowance_charges'] = [
            [
                'discount_id' => 1,
                'charge_indicator' => false,
                'allowance_charge_reason' => 'DESCUENTO GENERAL',
                'amount' => number_format($totals['discount'], 2, '.', ''),
                'base_amount' => number_format($totals['bruto'], 2, '.', ''),
            ],
        ];
        $params['tax_totals'] = [
            [
                'tax_id' => 1,
                'tax_amount' => $header->IVA > 0 ? number_format($totals['tax'], 2, '.', '') : 0,
                'percent' => $header->IVA > 0 ? '19.00' : '00.00',
                'taxable_amount' => number_format($totals['bruto'] - $totals['discount'], 2, '.', ''),
            ],
        ];

        $params['legal_monetary_totals'] = [
            'line_extension_amount' => number_format($totals['bruto'], 2, '.', ''),
            'tax_exclusive_amount' => number_format($totals['bruto'] - $totals['discount'], 2, '.', ''),
            'tax_inclusive_amount' => number_format($totals['bruto'] + $totals['tax'], 2, '.', ''),
            'allowance_total_amount' => number_format($totals['discount'], 2, '.', ''),
            'charge_total_amount' => '0.00',
            'payable_amount' => number_format(($totals['bruto'] - $totals['discount']) + $totals['tax'], 2, '.', ''),
        ];

        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('POST', "{$url}/ubl2.1/credit-note/", [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $document
     * @param string $entity
     * @return string
     *
     * @throws GuzzleException
     */
    protected function sendDebitNoteDian($document, string $entity = 'CIEV'): string
    {
        $data = DebitNote::where('NUMERO', '=', $document)
            ->first();

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('code', '=', $data->CODIGOCIUDAD)
            ->first();

        $params = [
            'notes' => $data->NOTAS,
            'number' => $data->NUMERO,
            'type_document_id' => 5,
            'date' => $data->FECHA,
            'time' => Carbon::now()->format('H:i:s'),
            'sendmail' => true,
            'sendmailtome' => false,
            'type_operation_id' => 5,
            'customer' => [
                'identification_number' => $data->NIT,
                'dv' => $data->DIGITOVERIFICACION,
                'name' => $data->RAZONSOCIAL,
                'phone' => $data->TELEFONO,
                'address' => $data->DIRECCION,
                'email' => $data->CORREO_FE,
                'merchant_registration' => '0000000-00',
                'type_document_identification_id' => $data->TIPOIDENTIFICACIONFISCAL == '31' ? 6 : 3,
                'type_organization_id' => $data->TIPOORGANIZACION,
                'municipality_id' => $city->id,
                'type_regime_id' => 1,
            ],
            'tax_totals' => [
                [
                    'tax_id' => 1,
                    'tax_amount' => number_format($data->IVA, 2, '.', ''),
                    'percent' => $data->IVA > 0 ? '19.00' : '00.00',
                    'taxable_amount' => number_format($data->SUBTOTAL, 2, '.', ''),
                ],
            ],
            'allowance_charges' => [
                [
                    'discount_id' => 1,
                    'charge_indicator' => false,
                    'allowance_charge_reason' => 'DESCUENTO GENERAL',
                    'amount' => number_format($data->DESCUENTO, 2, '.', ''),
                    'base_amount' => number_format($data->BRUTO, 2, '.', ''),
                ],
            ],

            'requested_monetary_totals' => [
                'line_extension_amount' => number_format($data->BRUTO, 2, '.', ''),
                'tax_exclusive_amount' => number_format($data->SUBTOTAL, 2, '.', ''),
                'tax_inclusive_amount' => number_format($data->BRUTO + $data->IVA, 2, '.', ''),
                'allowance_total_amount' => number_format($data->DESCUENTO, 2, '.', ''),
                'payable_amount' => number_format($data->SUBTOTAL + $data->IVA, 2, '.', ''),
            ],
            'debit_note_lines' => [
                [
                    'unit_measure_id' => 70,
                    'invoiced_quantity' => '1',
                    'line_extension_amount' => number_format($data->BRUTO, 2, '.', ''),
                    'free_of_charge_indicator' => false,
                    'tax_totals' => [
                        [
                            'tax_id' => 1,
                            'tax_amount' => number_format($data->IVA, 2, '.', ''),
                            'percent' => $data->IVA > 0 ? '19.00' : '0.00',
                            'taxable_amount' => number_format($data->SUBTOTAL, 2, '.', ''),
                        ],
                    ],
                    'description' => $data->DESCMOTIVO,
                    'notes' => '',
                    'code' => $data->MOTIVO,
                    'type_item_identification_id' => 4,
                    'price_amount' => number_format($data->BRUTO, 2, '.', ''),
                    'base_quantity' => '1',
                ],
            ],
        ];

        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('POST', "{$url}/ubl2.1/debit-note/", [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $invoice
     * @param string $entity
     * @return string
     *
     * @throws GuzzleException
     * @throws Throwable
     */
    protected function sendInvoiceExportDian($invoice, string $entity = 'CIEV'): string
    {
        //$this->updateTariffPosition($entity);

        $resolution = DB::connection('API_DIAN')
            ->table('resolutions')
            ->where('company_id', '=', $entity === 'CIEV' ? 3 : 4)
            ->where('type_document_id', '=', 2)
            ->first();

        $header = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian':  'PG_V_FE_FacturasTotalizadas_Dian')
            ->where('NUMERO', '=', $invoice)
            ->first();

        $detail = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasDetalladas':'PG_V_FE_FacturasDetalladas')
            ->where('factura', '=', $invoice)
            ->get();

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('code', '=', $header->CODIGOCIUDADCOMPLETO)
            ->first();

        $department = DB::connection('API_DIAN')
            ->table('departments')
            ->where('id', '=', $city->department_id)
            ->first();

        $country = DB::connection('API_DIAN')
            ->table('countries')
            ->where('id', '=', $department->country_id)
            ->first();

        $params = [
            'number' => $invoice,
            'type_document_id' => 2,
            'idcurrency' => 149,
            'calculationrate' => intval($header->TASA),
            'calculationratedate' => $header->FECHA,
            'date' => $header->FECHA,
            'time' => $header->HORA,
            'resolution_number' => $resolution->resolution,
            'prefix' => $resolution->prefix,
            'sendmail' => true,
            'email_cc_list' => trim($header->CORREOSCOPIA) == '' ? null : preg_split('/([,;])/', $header->CORREOSCOPIA),
            'notes' => $header->COMENTARIOS,
            'seller' => $header->NOMVENDEDOR,
            'ov' => $header->OV,
            'oc' => $header->OC,
            'customer' => [
                'identification_number' => $header->IDENTIFICACION,
                'dv' => $header->DIGITOVERIFICACION,
                'name' => $header->RAZONSOCIAL,
                'phone' => $header->TELEFONO,
                'address' => $header->DIRECCION,
                'email' => $header->CORREOFE,
                'merchant_registration' => '0000000-00',
                'type_document_identification_id' => $header->TIPODOCUMENTO == 31 ? 3 : 6,
                'type_organization_id' => $header->TIPOORGANIZACION,
                'municipality_id' => $city->id,
                'country_id' => $country->id,
                'type_regime_id' => $header->REGIMENFISCAL == 49 ? 2 : 1,
            ],
            'payment_form' => [
                'payment_form_id' => $header->DIAS > 0 ? 2 : 1,
                'payment_method_id' => 42,
                'payment_due_date' => $header->VENCIMIENTO,
                'duration_measure' => $header->DIAS,
            ],
            'deliveryterms' => [
                'special_terms' => 'COSTO SEGURO Y FLETE',
                'loss_risk_responsibility_code' => 'CIF',
                'loss_risk' => 'COSTO SEGURO Y FLETE',
            ],
            'allowance_charges' => [
                [
                    'charge_indicator' => true,
                    'allowance_charge_reason' => 'CARGO POR FLETES',
                    'amount' => number_format($header->FLETES_USD, 2, '.', ''),
                    'base_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                ],
                [
                    'charge_indicator' => true,
                    'allowance_charge_reason' => 'CARGO POR SEGUROS',
                    'amount' => number_format($header->SEGUROS_USD, 2, '.', ''),
                    'base_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                ],
            ],
            'legal_monetary_totals' => [
                'line_extension_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                'tax_exclusive_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                'tax_inclusive_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                'allowance_total_amount' => '0.00',
                'charge_total_amount' => number_format($header->FLETES_USD + $header->SEGUROS_USD, 2, '.', ''),
                'payable_amount' => number_format($header->BRUTO_USD + $header->FLETES_USD + $header->SEGUROS_USD, 2, '.', ''),
            ],
            'tax_totals' => [
                [
                    'tax_id' => 1,
                    'tax_amount' => '0.00',
                    'percent' => '0.00',
                    'taxable_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                ],
            ],
            'invoice_lines' => [],
            'free_items' => [],
        ];

        foreach ($detail as $item) {
            $notes = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
                ->table($entity === 'CIEV' ? 'CIEV_V_NotasFacturas': 'PG_V_NotasFacturas')
                ->where('Factura', '=', $invoice)
                ->where('Item', '=', $item->Item)
                ->where('OV', '=', $item->OV)
                ->get();

            if ($item->PrecioUSD > 0) {
                $params['invoice_lines'][] = [
                    'unit_measure_id' => $item->UM == 94 ? 70 : ($item->UM == 'KLG' ? 767 : 1016),
                    'invoiced_quantity' => $item->Cantidad,
                    'line_extension_amount' => number_format($item->TotalItemUSD, 2, '.', ''),
                    'free_of_charge_indicator' => $item->PrecioUSD == 0,
                    'allowance_charges' => [
                        [
                            'charge_indicator' => false,
                            'allowance_charge_reason' => 'DESCUENTO GENERAL',
                            'amount' => '0.00',
                            'base_amount' => number_format(floatval($item->TotalItemUSD), 2, '.', ''),
                        ],
                    ],
                    'tax_totals' => [
                        [
                            'tax_id' => 1,
                            'tax_amount' => '0.00',
                            'taxable_amount' => number_format($item->TotalItemUSD, 2, '.', ''),
                            'percent' => '0.00',
                        ],
                    ],
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'type_item_identification_id' => 4,
                    'price_amount' => number_format($item->PrecioUSD, 2, '.', ''),
                    'base_quantity' => $item->Cantidad,
                    'brand' => $item->Marca,
                    'art' => $item->ARTE,
                    'notes' => $notes,
                    'brandname' => 'EV',
                    'modelname' => $item->CodigoProducto,
                    'tariff_position' => $item->PosicionArancelaria,
                ];
            }
        }
        
        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('POST', "{$url}/ubl2.1/invoice-export/", [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $document
     * @param string $entity
     * @return string
     *
     * @throws GuzzleException
     */
    protected function sendCreditNoteExportDian($document, string $entity = 'CIEV'): string
    {
        $resolution = DB::connection('API_DIAN')
            ->table('resolutions')
            ->where( 'company_id', '=', $entity === 'CIEV' ? 3 : 4 )
            ->where('type_document_id', '=', '4')
            ->first();
        $header = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
            ->where('NUMERO', '=', $document)
            ->first();

        $detail = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasDetalladas': 'PG_V_FE_FacturasDetalladas')
            ->where('factura', '=', $document)
            ->get();

        /**
        $invoice_reference = strlen($header->OC) === 6 ? $header->OC : substr($header->OC, 2);

        $invoice_reference = ApiDocument::where('number', '=', $invoice_reference)
            ->where('state_document_id', '=', 1)
            ->first();**/

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('code', '=', $header->CODIGOCIUDADCOMPLETO)
            ->first();

        $department = DB::connection('API_DIAN')
            ->table('departments')
            ->where('id', '=', $city->department_id)
            ->first();

        $country = DB::connection('API_DIAN')
            ->table('countries')
            ->where('id', '=', $department->country_id)
            ->first();

        if ($header->OC && strlen(trim($header->OC)) > 0){
            $invoice_reference = " \r\n  DOCUMENTO REFERENCIA: {$header->OC}";
        }else {
            $invoice_reference = null;
        }

        $params = [
            'number' => $document,
            'type_document_id' => 4,
            'date' => $header->FECHA,
            'time' => $header->HORA,
            'resolution_number' => $resolution->resolution,
            'prefix' => $resolution->prefix,
            'notes' => $header->COMENTARIOS . $invoice_reference,
            'idcurrency' => 149,
            'calculationrate' => intval($header->TASA),
            'calculationratedate' => $header->FECHA,
            'customer' => [
                'identification_number' => $header->IDENTIFICACION,
                'dv' => $header->DIGITOVERIFICACION,
                'name' => $header->RAZONSOCIAL,
                'phone' => $header->TELEFONO,
                'address' => $header->DIRECCION,
                'email' => $header->CORREOFE,
                'merchant_registration' => '0000000-00',
                'type_document_identification_id' => $header->TIPODOCUMENTO == 31 ? 3 : 6,
                'type_organization_id' => $header->TIPOORGANIZACION,
                'municipality_id' => $city->id,
                'country_id' => $country->id,
                'type_regime_id' => $header->REGIMENFISCAL == 49 ? 2 : 1,
            ],
            'tax_totals' => [
                [
                    'tax_id' => 1,
                    'tax_amount' => '0.00',
                    'percent' => '0.00',
                    'taxable_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                ],
            ],

            'allowance_charges' => [
                [
                    'charge_indicator' => true,
                    'allowance_charge_reason' => 'CARGO POR FLETES',
                    'amount' => number_format($header->FLETES_USD, 2, '.', ''),
                    'base_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                ],
                [
                    'charge_indicator' => true,
                    'allowance_charge_reason' => 'CARGO POR SEGUROS',
                    'amount' => number_format($header->SEGUROS_USD, 2, '.', ''),
                    'base_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                ],
            ],

            'legal_monetary_totals' => [
                'line_extension_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                'tax_exclusive_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                'tax_inclusive_amount' => number_format($header->BRUTO_USD, 2, '.', ''),
                'allowance_total_amount' => '0.00',
                'charge_total_amount' => number_format($header->FLETES_USD + $header->SEGUROS_USD, 2, '.', ''),
                'payable_amount' => number_format($header->BRUTO_USD + $header->FLETES_USD + $header->SEGUROS_USD, 2, '.', ''),
            ],

            'credit_note_lines' => [],
            'free_items' => [],
        ];
        $params['type_operation_id'] = 8;

        foreach ($detail as $item) {
            $notes = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
                ->table($entity === 'CIEV' ? 'CIEV_V_NotasFacturas': 'PG_V_NotasFacturas')
                ->where('Factura', '=', $document)
                ->where('Item', '=', $item->Item)
                ->where('OV', '=', $item->OV)
                ->get();

            if ($item->PrecioUSD > 0) {
                $params['credit_note_lines'][] = [
                    'unit_measure_id' => $item->UM == 94 ? 70 : ($item->UM == 'KLG' ? 767 : 1016),
                    'invoiced_quantity' => $item->Cantidad,
                    'line_extension_amount' => number_format($item->TotalItemUSD, 2, '.', ''),
                    'free_of_charge_indicator' => $item->PrecioUSD == 0,
                    'allowance_charges' => [
                        [
                            'charge_indicator' => false,
                            'allowance_charge_reason' => 'DESCUENTO GENERAL',
                            'amount' => '0.00',
                            'base_amount' => number_format(floatval($item->TotalItemUSD), 2, '.', ''),
                        ],
                    ],
                    'tax_totals' => [
                        [
                            'tax_id' => 1,
                            'tax_amount' => '0.00',
                            'taxable_amount' => number_format($item->TotalItemUSD, 2, '.', ''),
                            'percent' => '0.00',
                        ],
                    ],
                    'description' => $item->DescripcionProducto,
                    'code' => $item->CodigoProducto,
                    'type_item_identification_id' => 4,
                    'price_amount' => number_format($item->PrecioUSD, 2, '.', ''),
                    'base_quantity' => $item->Cantidad,
                    'brand' => $item->Marca,
                    'art' => $item->ARTE,
                    'notes' => $notes,
                    'brandname' => 'EV',
                    'modelname' => $item->CodigoProducto,
                    'tariff_position' => $item->PosicionArancelaria,
                ];
            }
        }

        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('POST', "{$url}/ubl2.1/credit-note/", [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $document
     * @param $user_id
     * @param string|null $reason
     * @param string $entity
     * @return array
     * @throws Throwable
     */
    protected function import_document_dms($document, $user_id, string $reason = null, string $entity = 'CIEV'): array
    {
        $db_dms = $entity ===  'CIEV' ? 'DMS' : 'GOJA';
        $db_max = $entity === 'CIEV' ? 'MAX' : 'MAXPG';
        $type = $entity === 'CIEV' ? 'FAC' : 'FRA';

        DB::connection($db_dms)->beginTransaction();
        try {
            $user = User::find($user_id);

            $exist_document = DB::connection($db_dms)
                ->table('documentos')
                ->where('sw', '=', '1')
                ->where('tipo', '=', $type)
                ->where('numero', '=', $document)
                ->count();

            if ($exist_document > 0) {
                DB::connection($db_dms)->rollBack();
                throw new Exception('Este documento ya ha sido importando a DMS', 500);
            } else {
                $header = DB::connection($db_max)
                    ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
                    ->where('NUMERO', '=', $document)
                    ->first();

                if (! $header) {
                    throw new Exception('Documento no valido o inexistente en MAX', 500);
                }

                $total_paid = ($header->BRUTO + $header->IVA + $header->FLETES + $header->SEGUROS) - ($header->RTEFTE + $header->RTEIVA + $header->DESCUENTO);

                $model = null;
                $concept = null;
                $account_cxc = null;
                $account_sell = null;

                if ($entity === 'CIEV'){
                    if ($reason === '39'){
                        $model = $header->CODIVA === 'VATV' ? 'VPN' : 'VRC';
                        $concept = 1;
                        $account_cxc = '13050505';
                        $account_sell = '41209505';
                    }else {
                        switch ($header->TIPOCLIENTE) {
                            case 'ZF':
                                $model = 'VZF';
                                $concept = 6;
                                $account_cxc = '13050505';
                                if ($header->IVA > 0) {
                                    $account_sell = '41209515';
                                } else {
                                    $account_sell = '41209521';
                                }

                                break;
                            case 'CI':
                                if ($header->IVA > 0) {
                                    $model = 'VCI';
                                    $concept = 2;
                                    $account_cxc = '13050505';
                                    $account_sell = '41209515';
                                } else {
                                    $model = 'VCIS';
                                    $concept = 3;
                                    $account_cxc = '13050505';
                                    $account_sell = '41209510';
                                }
                                break;
                            case 'PN':
                                $model = 'VPN';
                                $concept = 1;
                                $account_cxc = '13050505';
                                $account_sell = '41209505';
                                break;
                            case 'RC':
                                $model = $header->CODIVA === 'VATV' ? 'VPN' : 'VRC';
                                $concept = 1;
                                $account_cxc = '13050505';
                                $account_sell = '41209505';
                                break;
                            case 'EX':
                                $model = 'VEX';
                                $concept = 1;
                                $account_cxc = '13051010';
                                $account_sell = '41209520';
                                break;
                        }
                    }
                }else {
                    switch ($header->TIPOCLIENTE) {
                        case 'PN':
                            $model = 'VPN';
                            $concept = 1;
                            $account_cxc = '13050505';
                            $account_sell = '41205005';
                            break;
                        case 'RC':
                            $model = $header->CODIVA === 'VATV' ? 'VPN' : 'VRC';
                            $concept = 1;
                            $account_cxc = '13050505';
                            $account_sell = '41205005';
                            break;
                        case 'EX':
                            $model = 'VEX';
                            $concept = 1;
                            $account_cxc = '13051010';
                            $account_sell = '41209520';
                            break;
                    }
                }

                DB::connection($db_dms)
                    ->table('documentos')
                    ->insert([
                        'sw' => '1',
                        'tipo' => $type,
                        'numero' => $document,
                        'nit' => $header->IDENTIFICACION,
                        'fecha' => $header->FECHA, /* crear input para fecha */
                        'condicion' => $header->PLAZO === '00' ? '10' : $header->PLAZO,
                        'vencimiento' => $header->VENCIMIENTO, /*mismo valor de fecha*/
                        'valor_total' => $total_paid, /* valor pagado en RC*/
                        'iva' => round($header->IVA),
                        'retencion' => round($header->RTEFTE), /* retencion RC*/
                        'retencion_causada' => 0,
                        'fletes' => round($header->FLETES + $header->SEGUROS),
                        'retencion_iva' => round($header->RTEIVA),
                        'retencion_ica' => 0,
                        'descuento_pie' => round($header->DESCUENTO),
                        'iva_fletes' => 0,
                        'costo' => 0,
                        'vendedor' => $header->CODVENDEDOR, /* vendedor asociado a la factura*/
                        'valor_aplicado' => 0, /* valor pagado en RC*/
                        'anulado' => 0,
                        'modelo' => $model,
                        'documento' => $header->OV, /* dejar en blanco*/
                        'notas' => 'Importado desde EVPIU el '.Carbon::now()->format('Y-m-d'), /* comentarios del RC*/
                        'usuario' => $user->username,
                        'pc' => gethostname(),
                        'fecha_hora' => Carbon::now(),
                        'moneda' => $reason === '39' ? 'US' : ($header->MONEDA === 'USD' ? 'US' : null),
                        'tasa' => $reason === '39' ? number_format($header->TASA, 2, '.', '') : ($header->MONEDA === 'USD' ? number_format($header->TASA, 2, '.', '') : null),
                        'retencion2' => 0,
                        'retencion3' => 0,
                        'bodega' => 1,
                        'impoconsumo' => 0,
                        'descuento2' => 0,
                        'duracion' => 1,
                        'concepto' => $concept,
                        'impuesto_deporte' => 0,
                        'centro_doc' => $reason === '39' ? 0 : ($header->MONEDA === 'USD' ? 0 : $header->MOTIVO),
                        'valor_mercancia' => round($header->BRUTO),
                    ]);

                $seq = 2;

                DB::connection($db_dms)
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $type,
                        'numero' => $document,
                        'seq' => 1,
                        'cuenta' => $account_cxc,
                        'centro' => 0,
                        'nit' => $header->IDENTIFICACION,
                        'fec' => $header->FECHA,
                        'valor' => round($total_paid),
                        'base' => 0,
                        'documento' => $header->OV,
                    ]);

                if($reason === '38'){
                    DB::connection($db_dms)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $type,
                            'numero' => $document,
                            'seq' => 2,
                            'cuenta' => $header->IVA > 0 ? '41209555' : '41209560',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => -round($header->BRUTO - $header->DESCUENTO),
                            'base' => 0,
                            'documento' => $header->OV,
                        ]);
                }else {
                    DB::connection($db_dms)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $type,
                            'numero' => $document,
                            'seq' => 2,
                            'cuenta' => $account_sell,
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => -round($header->BRUTO - $header->DESCUENTO),
                            'base' => 0,
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->IVA > 0) {
                    $seq++;
                    DB::connection($db_dms)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $type,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '24080507',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => -round($header->IVA),
                            'base' => round($header->BRUTO - $header->DESCUENTO),
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->RTEFTE > 0) {
                    $seq++;
                    DB::connection($db_dms)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $type,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => $entity === 'CIEV' ? '13551501' : '13551505',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => round($header->RTEFTE),
                            'base' => round($header->BRUTO - $header->DESCUENTO),
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->RTEIVA > 0) {
                    $seq++;
                    DB::connection($db_dms)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $type,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '13551705',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => round($header->RTEIVA),
                            'base' => round($header->BRUTO - $header->DESCUENTO),
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->FLETES > 0 || $header->SEGUROS > 0) {
                    $seq++;
                    DB::connection($db_dms)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $type,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '42505005',
                            'centro' => 13,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => -round(($header->FLETES + $header->SEGUROS)),
                            'base' => 0,
                            'documento' => $header->OV,
                        ]);
                }

                DB::connection($db_dms)->commit();

                return [
                    'document' => $document,
                    'code' => 200,
                    'msg' => 'Importación exitosa',
                ];
            }
        } catch (Exception $e) {
            DB::connection($db_dms)->rollBack();

            return [
                'document' => $document,
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
            ];
        }
    }


    /**
     * @param $document
     * @param $user_id
     * @return array
     * @throws Throwable
     */
    protected function import_credit_note_dms($document, $user_id): array
    {
        DB::connection('GOJA')->beginTransaction();
        try {
            $memo_header = DB::connection('MAXPG')
                ->table('PG_V_FE_FacturasTotalizadas_Dian')
                ->where('NUMERO', '=', $document)
                ->where('TIPODOC', '=', 'CR')
                ->first();

            $associate_vendor = DB::connection('GOJA')
                ->table('terceros')
                ->where('nit', '=', explode('-', $memo_header->IDENTIFICACION))
                ->pluck('vendedor')
                ->first();

            $invoice_movements = DB::connection('DMS')
                ->table('movimiento')
                ->where('tipo', '=', 'FRA')
                ->where('numero', '=', $memo_header->OC)
                ->whereIn('seq', [1, 2])
                ->get();

            $sequence_flag = 2;

            $total_paid = ($memo_header->SUBTOTAL + $memo_header->IVA + $memo_header->FLETES + $memo_header->SEGUROS) - ($memo_header->RTEFTE + $memo_header->RTEIVA);

            DB::connection('DMS')
                ->table('documentos')
                ->insert([
                    'sw' => '23',
                    'tipo' => 'NCCD',
                    'numero' => $memo_header->NUMERO,
                    'nit' => $memo_header->IDENTIFICACION,
                    'fecha' => $memo_header->FECHA, /* crear input para fecha */
                    'condicion' => 0,
                    'vencimiento' => $memo_header->FECHA, /*mismo valor de fecha*/
                    'valor_total' => $total_paid, /* valor pagado en RC*/
                    'iva' => $memo_header->IVA,
                    'retencion' => $memo_header->RTEFTE, /* retencion RC*/
                    'retencion_causada' => 0,
                    'retencion_iva' => $memo_header->RTEIVA,
                    'retencion_ica' => 0,
                    'descuento_pie' => $memo_header->DESCUENTO,
                    'fletes' => $memo_header->FLETES + $memo_header->SEGUROS,
                    'iva_fletes' => 0,
                    'costo' => 0,
                    'vendedor' => intval($associate_vendor), /* vendedor asociado a la factura*/
                    'valor_aplicado' => 0, /* valor pagado en RC*/
                    'anulado' => 0,
                    'modelo' => '*',
                    'documento' => $memo_header->OC, /* dejar en blanco*/
                    'notas' => $memo_header->COMENTARIOS, /* comentarios del RC*/
                    'usuario' => Auth::user()->username,
                    'pc' => gethostname(),
                    'fecha_hora' => Carbon::now(),
                    'retencion2' => 0,
                    'retencion3' => 0,
                    'bodega' => 1,
                    'impoconsumo' => 0,
                    'descuento2' => 0,
                    'duracion' => 1,
                    'concepto' => 1,
                    'vencimiento_presup' => Carbon::now(),
                    'exportado' => 1,
                    'impuesto_deporte' => 0,
                    'tasa' => $memo_header->TASA,
                    'centro_doc' => 0,
                    'valor_mercancia' => $memo_header->BRUTO,
                ]);

            DB::connection('DMS')
                ->table('movimiento')
                ->insert([
                    'tipo' => 'NCCD',
                    'numero' => $memo_header->NUMERO,
                    'seq' => 1,
                    'cuenta' => $invoice_movements->where('seq', '=', 1)->first()->cuenta,
                    'centro' => 0,
                    'nit' => $memo_header->IDENTIFICACION,
                    'fec' => $memo_header->FECHA,
                    'valor' => gmp_neg($total_paid),
                    'documento' => $memo_header->OC,
                ]);

            $original_account = match ($invoice_movements->where('seq', '=', 2)->first()->cuenta) {
                '41209505', '41209521' => 41752005,
                '41209510' => 41752010,
                '41209515' => 41752015,
                '41209520' => 41752020,
            };

            DB::connection('DMS')
                ->table('movimiento')
                ->insert([
                    'tipo' => 'NCCD',
                    'numero' => $memo_header->NUMERO,
                    'seq' => 2,
                    'cuenta' => $original_account, // seq original 3 se cambia por solicitud de martin issue 1406
                    'centro' => 0,
                    'nit' => $memo_header->IDENTIFICACION,
                    'fec' => $memo_header->FECHA,
                    'valor' => $memo_header->SUBTOTAL,
                    'documento' => $memo_header->OC,
                ]);

            if ($memo_header->IVA > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCCD',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '24080521',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => $memo_header->IVA,
                        'base' => $memo_header->BASEIVA,
                        'documento' => $memo_header->OC,
                    ]);
            }

            if ($memo_header->RTEFTE > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCCD',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '13551501',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => -abs($memo_header->RTEFTE),
                        'base' => $memo_header->BASEIVA,
                        'documento' => $memo_header->OC,
                    ]);
            }

            if ($memo_header->RTEIVA > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCCD',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '13551705',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => -abs($memo_header->RTEIVA),
                        'documento' => $memo_header->OC,
                    ]);
            }

            DB::connection('GOJA')->commit();

            return [
                'msg' => 'success store document',
                'code' => 200,
            ];

        }catch (Exception $e){
            DB::connection('GOJA')->rollBack();

            return [
                'msg' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * @param string $entity
     * @return bool
     * @throws Throwable
     */
    protected function updateTariffPosition(string $entity = 'CIEV'): bool
    {
        $db_max = $entity === 'CIEV' ? 'MAX' : 'MAXPG';
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'PUNTILL 76.16.10.00.00' WHERE PRTNUM_29 LIKE '301%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.L 83.08.20.00.00' WHERE PRTNUM_29 LIKE '302__L%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.H 83.08.10.11.00' WHERE PRTNUM_29 LIKE '302__H%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.A 83.08.10.12.00' WHERE PRTNUM_29 LIKE '302__A%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'ARANDE 83.08.10.19.00' WHERE PRTNUM_29 LIKE '303%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'HEBILLA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '304%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'HEBILLA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '305%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BROCHE 96.06.10.00.00' WHERE PRTNUM_29 LIKE '307%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BROCHE 96.06.10.00.00' WHERE PRTNUM_29 LIKE '807%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BROCHE 96.06.10.00.00' WHERE PRTNUM_29 LIKE '807%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '310%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '311%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '312%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '314%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '316%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '318%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '339%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '342%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE '344%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'BOTON  96.06.22.00.00' WHERE PRTNUM_29 LIKE 'T04%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'PUNTERA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '347%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '319%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '326%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE 'T06%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '330%'");
            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE 'T09%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'DIJE 83.08.90.00.00' WHERE PRTNUM_29 LIKE '332%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'GANCHO 83.08.90.00.00' WHERE PRTNUM_29 LIKE '806%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'OJAL.P 39.26.90.90.90' WHERE PRTNUM_29 IN('80643','80647') OR PRTNUM_29 LIKE '802%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 LIKE '819%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'TROQUEL 82.07.90.00.00' WHERE PRTNUM_29 LIKE '820%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'MANUAL 84.63.90.10.00' WHERE PRTNUM_29 LIKE '821%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'PUNTERA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '847%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'HEBILLA 83.08.90.00.00' WHERE PRTNUM_29 LIKE '306%'");

            DB::connection($db_max)->statement("UPDATE Part_Sales SET UDFREF_29 = 'REMACHE 83.08.20.00.00' WHERE PRTNUM_29 = '30201A080000000'");

            DB::connection($db_max)->commit();
            return true;
        }catch (Exception $e){
            DB::connection($db_max)->rollBack();
            return false;
        }
    }

    /**
     * @param $invoice
     * @return void
     * @throws Throwable
     */
    protected function updateRetentionGoja($invoice): void
    {
        DB::connection('MAXPG')->beginTransaction();
        try {
            $document = DB::connection('MAXPG')
                ->table('PG_V_FE_FacturasTotalizadas_Dian')
                ->where('NUMERO', '=', $invoice)
                ->first();

            $detail = DB::connection('MAXPG')
                ->table('PG_V_FE_FacturasDetalladas')
                ->where('factura', '=', $invoice)
                ->get()
                ->toArray();

            if ($document->IVA > 0 && $document->TIPOCLIENTE !== 'PN') {
                $crr = current_retention_rates(Carbon::now()->year);

                $type = array_search('servicio', array_column($detail, 'DescripcionProducto'))
                    ? 'SERVICIOS'
                    : 'VENTAS';

                $total = $type === 'SERVICIOS'
                    ? ($document->SUBTOTAL * 4) / 100
                    : ($document->SUBTOTAL * 2.5) / 100;

                if ($document->SUBTOTAL > $crr->where('Tipo', '=', $type)->first()->Base) {
                    DB::connection('MAXPG')
                        ->table('Invoice_Master_EXT')
                        ->updateOrInsert([
                            'INVCE_31' => str_pad($invoice, '8', '0', STR_PAD_LEFT),
                            'STYPE_31' => 'CU'
                        ],[
                            'RTEFTE' => round($total)
                        ]);
                }else {
                    DB::connection('MAXPG')
                        ->table('Invoice_Master_EXT')
                        ->updateOrInsert([
                            'INVCE_31' => str_pad($invoice, '8', '0', STR_PAD_LEFT),
                            'STYPE_31' => 'CU'
                        ],[
                            'RTEFTE' => 0
                        ]);
                }


                if ($document->IVA > $crr->where('Tipo', '=', 'RI-VENTAS')->first()->Base && $document->CLIENTE === '890926617'){
                    $percent = $crr->where('Tipo', '=', 'RI-VENTAS')->first()->Tasa;
                    $total_ri = ($document->IVA * $percent) / 100;

                    DB::connection('MAXPG')
                        ->table('Invoice_Master_EXT')
                        ->updateOrInsert([
                            'INVCE_31' => str_pad($invoice, '8', '0', STR_PAD_LEFT),
                            'STYPE_31' => 'CU'
                        ],[
                            'RETEIVA' => round($total_ri)
                        ]);
                }else {
                    DB::connection('MAXPG')
                        ->table('Invoice_Master_EXT')
                        ->updateOrInsert([
                            'INVCE_31' => str_pad($invoice, '8', '0', STR_PAD_LEFT),
                            'STYPE_31' => 'CU'
                        ],[
                            'RETEIVA' => 0
                        ]);
                }
            }

            DB::connection('MAXPG')->commit();
        } catch (Exception $ex) {
            DB::connection('MAXPG')->rollBack();
        }
    }
}
