<?php

namespace App\Http\Controllers;

use App\Models\SupportDocumentHeader;
use App\Models\SupportDocumentProduct;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class SupportDocumentController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.electronic-billing.support-document|goja.application.support-document');
    }

    /**
     * @return Response
     */
    public function index($entity)
    {
        $support_documents = DB::table('V_SUPPORT_DOCUMENTS')
            ->where('entity', '=', $entity)
            ->get();

        return Inertia::render('Applications/SupportDocument/Index', [
            'support_documents' => $support_documents,
            'entity' => $entity,
        ]);
    }

    /**
     * @param $entity
     * @param $id
     * @return JsonResponse
     */
    public function view($entity, $id){
        $support_document = SupportDocumentHeader::with('details.product', 'logs.user')
            ->where('entity', '=', $entity)
            ->find($id);

        return response()->json($support_document, 200);
    }

    /**
     * @param  Request  $request
     * @param $entity
     * @return JsonResponse
     */
    public function store(Request $request, $entity)
    {
        DB::beginTransaction();
        try {
            $support_document = new SupportDocumentHeader();
            $support_document->consecutive = last_support_document_consecutive($entity, 'support-document');
            $support_document->seller_document = $request->provider;
            $support_document->notes = $request->notes;
            $support_document->transaction_date = Carbon::parse($request->date)->format('Y-m-d');
            $support_document->created_id = Auth::id();
            $support_document->payment_form = $request->payment_form;
            $support_document->state = 'pending';
            $support_document->entity = $entity;
            $support_document->type = 'support-document';
            $support_document->save();

            foreach ($request->items as $item) {
                $support_document
                    ->details()
                    ->create([
                        'product_id' => $item['id'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'retention' => $item['retention'],
                        'measurement' => $item['measurement'],
                        'type' => $item['type'],
                        'type_transmition_id' => $item['type_generation_transmition_id'],
                        'transmition_date' => array_key_exists('transmition_date', $item) ? Carbon::parse($item['transmition_date'])->format('Y-m-d') : Carbon::parse($request->date)->format('Y-m-d'),
                    ]);
            }

            $support_document->logs()->create([
                'user_id' => Auth::id(),
                'description' => 'creo un nuevo documento soporte',
            ]);

            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function create($entity)
    {
        return Inertia::render('Applications/SupportDocument/Create', [
            'entity' => $entity,
        ]);
    }

    /**
     * @param  Request  $request
     * @param $entity
     * @return JsonResponse
     */
    public function search_provider(Request $request, $entity)
    {
        $q = $request->get('q');
        $connection = $entity === 'CIEV' ? 'DMS' : 'GOJA';
        $table = $entity === 'CIEV' ? 'V_CIEV_Terceros' : 'V_PG_Terceros';

        $providers = DB::connection($connection)
            ->table($table)
            ->where('TIPO_TERCERO', 'like', '%PROVEEDOR%')
            ->where(function ($query) use ($q) {
                $query->where('nit', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%");
            })->get();

        return response()->json($providers, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_product(Request $request)
    {
        $q = $request->get('q');

        $products = SupportDocumentProduct::where('description', 'like', "%{$q}%")
            ->get();

        return response()->json($products, 200);
    }

    /**
     * @param $description
     * @return JsonResponse
     */
    public function product_validate_description($description)
    {
        $value = SupportDocumentProduct::where('description', $description)->count();

        return response()->json(! $value > 0, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function product_store(Request $request)
    {
        $product = new SupportDocumentProduct($request->all());
        $product->created_id = Auth::id();
        $product->save();

        return response()->json('success', 200);
    }

    /**
     * @param  Request  $request
     * @param $entity
     * @return JsonResponse
     */
    public function send_dian(Request $request, $entity)
    {
        try {
            $resolution = DB::connection('API_DIAN')
                ->table('resolutions')
                ->where('company_id', '=', $entity === 'CIEV' ? 3 : 4)
                ->where('type_document_id', '=', '11')
                ->first();

            $support_document = SupportDocumentHeader::with('details.product')
                ->where('id', '=', $request->id)
                ->first();

            $city = DB::connection('API_DIAN')
                ->table('municipalities')
                ->where('code', '=', $support_document->provider->y_ciudad)
                ->first();

            $params = [
                'number' => $support_document->consecutive,
                'type_document_id' => 11,
                'date' => $support_document->transaction_date,
                'time' => '05:00:00',
                'resolution_number' => $resolution->resolution,
                'prefix' => $resolution->prefix,
                'sendmail' => false,
                'notes' => $support_document->notes,
                'seller' => [
                    'identification_number' => $support_document->provider->nit,
                    'dv' => calculateDV($support_document->provider->nit),
                    'name' => $support_document->provider->nombres,
                    'phone' => is_numeric($support_document->provider->telefono_1) ? $support_document->provider->telefono_1 : null,
                    'address' => $support_document->provider->direccion,
                    'postal_zone' => $city !== null ? $city->code : '05001',
                    'email' => $support_document->provider->mail,
                    'merchant_registration' => '0000000-00',
                    'type_document_identification_id' => trim($support_document->provider->tipo_identificacion) == 'C' ? 3 : 6,
                    'type_organization_id' => trim($support_document->provider->tipo_identificacion) == 'N' ? 1 : 2,
                    'municipality_id' => $city !== null ? $city->id : 1,
                    'type_regime_id' => 2,
                ],
                'payment_form' => [
                    'payment_form_id' => $support_document->payment_form,
                    'payment_method_id' => 42,
                    'payment_due_date' => $support_document->transaction_date,
                    'duration_measure' => '0',
                ],
                'legal_monetary_totals' => [
                    'line_extension_amount' => $support_document->details->sum('total'),
                    'tax_exclusive_amount' => '0.00',
                    'tax_inclusive_amount' => $support_document->details->sum('total'),
                    'allowance_total_amount' => '0.00',
                    'charge_total_amount' => '0.00',
                    'payable_amount' => $support_document->details->sum('total'),
                ],

                'with_holding_tax_total' => [
                    [
                        'tax_id' => 6,
                        'tax_amount' => number_format($support_document->details->sum('retention'), 2, '.', ''),
                        'percent' => $this->calculate_retention($support_document->details, 'retention'),
                        'taxable_amount' => $this->calculate_retention($support_document->details, 'base'),
                    ],
                ],

                'invoice_lines' => [],
            ];

            foreach ($support_document->details as $row) {
                $params['invoice_lines'][] = [
                    'unit_measure_id' => 70,
                    'invoiced_quantity' => $row->quantity,
                    'line_extension_amount' => $row->price,
                    'free_of_charge_indicator' => false,
                    'description' => $row->product->description,
                    'code' => (string) $row->product->id,
                    'type_item_identification_id' => 4,
                    'price_amount' => $row->price,
                    'base_quantity' => $row->quantity,
                    'notes' => '',
                    'type_generation_transmission_id' => $row->type_transmition_id,
                    'operation_date' => $row->transmition_date,
                ];
            }

            if ($entity === 'CIEV') {
                $token = config('apidian.token');
                $url = config('apidian.url');
                $test_set = config('apidian.set_test');
            } elseif ($entity === 'GOJA') {
                $token = config('apidian.token_goja');
                $url = config('apidian.url');
                $test_set = config('apidian.set_test_goja');
            } else {
                throw new \Exception('Error al obtener variables de entorno de la API DIAN', 500);
            }

            $client = new Client(['base_uri' => $url]);

            $headers = [
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ];

            $response = $client->request('POST', "{$url}/ubl2.1/support-document/{$test_set}", [
                'headers' => $headers,
                'json' => $params,
            ]);

            $response = json_decode($response->getBody()->getContents());

            $message = $response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->ErrorMessage->string ?? '';

            $message_str = is_array($message)
                ? implode('|', $message)
                : $message;

            if ($response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid == 'true') {
                $support_document->state = 'success';
                $support_document->save();

                $support_document->logs()->create([
                    'user_id' => Auth::id(),
                    'description' => 'Envio el documento a la DIAN',
                ]);

                $support_document->logs()->create([
                    'user_id' => Auth::id(),
                    'description' => 'Documento autorizado por la DIAN',
                ]);

                DB::commit();
            } else {
                throw new Exception($message_str, 500);
            }

            $support_documents = DB::table('V_SUPPORT_DOCUMENTS')
                ->where('entity', '=', $entity)
                ->get();

            return response()->json([
                'support_documents' => $support_documents,
                'document' => $support_document->consecutive,
                'status' => $response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid,
                'status_code' => $response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->StatusCode,
                'error_message' => $message_str,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        } catch (GuzzleException $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function adjust_note(Request $request)
    {
        DB::beginTransaction();
        try {
            $original_document = SupportDocumentHeader::with('details.product')
                ->where('entity', '=', $request->entity)
                ->where('consecutive', '=', $request->consecutive)
                ->first();

            $document_dian = DB::connection('API_DIAN')
                ->table('documents')
                ->where('identification_number', '=', $request->entity === 'CIEV' ? 890926617 : 900349726)
                ->where('state_document_id', '=', 1)
                ->where('type_document_id', '=', 11)
                ->where('number', '=', $request->consecutive)
                ->first();

            $json_api = json_decode($document_dian->request_api);

            if ($original_document && $document_dian) {
                $adjust_note = new SupportDocumentHeader;
                $adjust_note->consecutive = last_support_document_consecutive($request->entity, 'adjust-note');
                $adjust_note->seller_document = $original_document->seller_document;
                $adjust_note->notes = "ANULACIÓN DOCUMENTO SOPORTE {$document_dian->prefix}{$document_dian->number} POR ERROR DE DIGITACIÓN";
                $adjust_note->transaction_date = Carbon::now()->format('Y-m-d');
                $adjust_note->created_id = Auth::id();
                $adjust_note->payment_form = $original_document->payment_form;
                $adjust_note->state = 'pending';
                $adjust_note->entity = $request->entity;
                $adjust_note->type = 'adjust-note';
                $adjust_note->save();

                foreach ($original_document->details as $item) {
                    $adjust_note->details()->create([
                        'product_id' => $item->product_id,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'retention' => $item->retention,
                        'measurement' => $item->measurement,
                        'type' => $item->type,
                        'type_transmition_id' => $item->type_transmition_id,
                        'transmition_date' => $item->transmition_date ? Carbon::parse($item->transmition_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                    ]);
                }

                $adjust_note->logs()->create([
                    'user_id' => Auth::id(),
                    'description' => 'creo un nuevo documento de ajuste',
                ]);

                $params = [
                    'billing_reference' => [
                        'number' => "{$document_dian->prefix}{$document_dian->number}",
                        'uuid' => $document_dian->cufe,
                        'issue_date' => Carbon::parse($document_dian->date_issue)->format('Y-m-d'),
                        'scheme_name' => 'CUDS-SHA384',
                    ],
                    'discrepancyresponsecode' => 2,
                    'discrepancyresponsedescription' => "ANULACIÓN DOCUMENTO SOPORTE {$document_dian->prefix}{$document_dian->number} POR ERROR DE DIGITACIÓN",
                    'notes' => "ANULACIÓN DOCUMENTO SOPORTE {$document_dian->prefix}{$document_dian->number} POR ERROR DE DIGITACIÓN",
                    'resolution_number' => '',
                    'prefix' => 'NADS',
                    'number' => $adjust_note->consecutive,
                    'type_document_id' => 13,
                    'type_operation_id' => 10,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'time' => Carbon::now()->format('H:i:s'),
                    'sendmail' => false,
                    'sendmailtome' => false,
                    'seller' => $json_api->seller,
                    'legal_monetary_totals' => $json_api->legal_monetary_totals,
                    'with_holding_tax_total' => $json_api->with_holding_tax_total,
                    'credit_note_lines' => [],
                ];

                foreach ($json_api->invoice_lines as $invoice_line) {
                    $invoice_line->modelname = $invoice_line->description;
                    $invoice_line->brandname = $invoice_line->description;
                    $params['credit_note_lines'][] = $invoice_line;
                }

                if ($request->entity === 'CIEV') {
                    $token = config('apidian.token');
                    $url = config('apidian.url');
                    $test_set = config('apidian.set_test');
                } elseif ($request->entity === 'GOJA') {
                    $token = config('apidian.token_goja');
                    $url = config('apidian.url');
                    $test_set = config('apidian.set_test_goja');
                } else {
                    throw new Exception('Error al obtener variables de entorno de la API DIAN', 500);
                }

                $client = new Client(['base_uri' => $url]);

                $headers = [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ];

                $response = $client->request('POST', "{$url}/ubl2.1/support-document-adjust-note/{$test_set}", [
                    'headers' => $headers,
                    'json' => $params,
                ]);

                $response = json_decode($response->getBody()->getContents());
                $message = $response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->ErrorMessage->string ?? '';

                $message_str = is_array($message)
                    ? implode('|', $message)
                    : $message;

                if ($response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid == 'true' || str_contains($message, 'Documento procesado anteriormente')) {
                    $adjust_note->state = 'success';
                    $adjust_note->save();

                    $adjust_note->logs()->create([
                        'user_id' => Auth::id(),
                        'description' => 'Envio el documento a la DIAN',
                    ]);

                    $adjust_note->logs()->create([
                        'user_id' => Auth::id(),
                        'description' => 'Documento autorizado por la DIAN',
                    ]);

                    DB::commit();
                } else {
                    throw new Exception($message_str, 500);
                }
            } else {
                throw new Exception("Documento soporte con numero $request->consecutive no ha sido encontrado", 500);
            }

            $support_documents = DB::table('V_SUPPORT_DOCUMENTS')
                ->where('entity', '=', $request->entity)
                ->get();

            return response()->json([
                'support_documents' => $support_documents,
                'document' => $adjust_note->consecutive,
                'status' => $response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid,
                'status_code' => $response->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->StatusCode,
                'error_message' => $message_str,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        } catch (GuzzleException $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $items
     * @param $type
     * @return string
     */
    protected function calculate_retention($items, $type)
    {
        $total_retention = $items->sum('retention');
        $base = $items->where('retention', '>', 0)->sum('total');

        return $type === 'retention'
            ? ($total_retention > 0 ? number_format(($total_retention * 100) / $base, 2, '.', '') : 0)
            : number_format($base, 2, '.', '');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response|mixed|string|void
     *
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function print($id)
    {
        $support_document = SupportDocumentHeader::with('details.product')->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.support_document.header', compact('support_document')));
        $pdf->SetHTMLFooter(View::make('pdfs.support_document.footer', compact('support_document')));
        $pdf->WriteHTML(View::make('pdfs.support_document.template', compact('support_document')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @throws MpdfException
     */
    protected function initMPdf(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'fontdata' => $fontData + [
                'Roboto' => [
                    'R' => 'Roboto-Regular.ttf',
                    'B' => 'Roboto-Bold.ttf',
                    'I' => 'Roboto-Italic.ttf',
                ],
            ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 30,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/orders/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }
}
