<?php

namespace App\Http\Controllers;

use App\Custom\ReadXML;
use App\Imports\InvoiceProviderVerificationImport;
use App\Models\SupplierPurchase;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZanySoft\Zip\Facades\Zip;

class SupplierPurchaseController extends Controller
{
    //use ReadXMLTrait;
    use ReadXML;

    /**
     * @return Response
     */
    public function index()
    {
        $supplier_purchases = SupplierPurchase::with('upload_user')
            ->where('entity', '=', 'CIEV')
            ->get();

        return Inertia::render('Applications/SupplierPurchase/Index', [
            'supplier_purchases' => $supplier_purchases,
        ]);
    }

    /**
     * @param  Request  $request
     * @return BinaryFileResponse
     */
    public function download_file(Request $request): BinaryFileResponse
    {
        $file = $request->file_name;

        return response()->download(storage_path('app/supplier_purchases/'.$file), $file);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function change_state(Request $request)
    {
        DB::beginTransaction();
        try {
            $document = SupplierPurchase::find($request->id);

            $event = DB::connection('API_DIAN')
                ->table('events')
                ->where('code', '=', $request->state)
                ->first();

            $params = [
                'event_id' => $event->id,
                'sender' => [
                    'identification_number' => 890926617,
                    'dv' => 8,
                    'name' => 'CI ESTRADA VELASQUEZ Y CIA SAS',
                    'tax_id' => 1,
                ],
                'receiver' => [
                    'identification_number' => $document->supplier['CompanyID'],
                    'dv' => calculateDV($document->supplier['CompanyID']),
                    'name' => $document->supplier['Name'],
                    'tax_id' => 1,
                ],
                'document_reference' => [
                    'number' => str_replace($document->document_information['Prefix'], '', $document->document_information['ID']),
                    'prefix' => $document->document_information['Prefix'],
                    'uuid' => $document->document_information['UUID'],
                    'type_document_id' => 1,
                ],
            ];

            if ($request->state === '031') {
                $params['type_rejection_id'] = 1;
            }

            $token = config('apidian.token');
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
                $document->dian_state = $request->state;
                $request->state === '032'
                    ? $document->received_by = Auth::id()
                    : $document->accepted_by = Auth::id();
                $document->save();
                DB::commit();

                $supplier_purchases = SupplierPurchase::with('upload_user')
                    ->where('entity', '=', 'CIEV')
                    ->get();

                return response()->json($supplier_purchases, 200);
            } else {
                return response()->json([
                    'StatusCode' => $result->StatusCode,
                    'StatusDescription' => $result->StatusDescription,
                    'ErrorMessage' => $result->ErrorMessage,
                ], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function notify_reception(Request $request)
    {
        DB::beginTransaction();
        try {
            $document = SupplierPurchase::find($request->id);

            $params = [
                'event_id' => $request->state,
                'sender' => [
                    'identification_number' => 890926617,
                    'dv' => 8,
                    'name' => 'CI ESTRADA VELASQUEZ Y CIA SAS',
                    'tax_id' => 1,
                ],
                'receiver' => [
                    'identification_number' => $document->supplier['CompanyID'],
                    'dv' => calculateDV($document->supplier['CompanyID']),
                    'name' => $document->supplier['Name'],
                    'tax_id' => 1,
                ],
                'document_reference' => [
                    'number' => str_replace($document->document_information['Prefix'], '', $document->document_information['ID']),
                    'prefix' => strlen($document->document_information['Prefix']) > 0 ? $document->document_information['Prefix'] : null,
                    'uuid' => $document->document_information['UUID'],
                    'type_document_id' => 1,
                ],
            ];

            $json = json_encode($params);

            $token = config('apidian.token');
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

            if ($result->IsValid === 'true' || $result->IsValid === 'false' && str_contains($result->ErrorMessage->string, 'Regla: 90')) {
                $event = DB::connection('API_DIAN')
                    ->table('events')
                    ->where('id', '=', $request->state)
                    ->first();

                $document->dian_state = $event->code;
                $document->work_center = $request->work_center;
                $document->classification = $request->classification;
                $document->save();
                DB::commit();

                $supplier_purchases = SupplierPurchase::with('upload_user')
                    ->where('entity', '=', 'CIEV')
                    ->get();

                return response()->json($supplier_purchases, 200);
            } else {
                return response()->json([
                    'StatusCode' => $result->StatusCode,
                    'StatusDescription' => $result->StatusDescription,
                    'ErrorMessage' => $result->ErrorMessage,
                ], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * @return Response
     */
    public function work_center()
    {
        $user_works = \auth()->user()->work_center_array;

        if (auth()->user()->hasRole('super-admin') || auth()->user()->can('supplier-purchases.work-center.show-all')) {
            $supplier_purchases = SupplierPurchase::with('upload_user', 'received_user', 'accepted_user')
                ->where('entity', '=', 'CIEV')
                ->whereIn('dian_state', ['030', '032', '033'])
                ->orderBy('created_at' )
                ->get();
        } else {
            $supplier_purchases = SupplierPurchase::with('upload_user', 'received_user', 'accepted_user')
                ->where('entity', '=', 'CIEV')
                ->whereIn('dian_state', ['030', '032', '033'])
                ->whereIn('work_center', $user_works)
                ->orderBy('created_at' )
                ->get();
        }

        return Inertia::render('Applications/SupplierPurchase/WorkCenter', [
            'supplier_purchases' => $supplier_purchases,
        ]);
    }

    /**
     * @return Response
     */
    public function audit()
    {
        return Inertia::render('Applications/SupplierPurchase/Audit');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function audit_check(Request $request)
    {
        set_time_limit(0);

        try {
            $import = new InvoiceProviderVerificationImport;
            Excel::import($import, $request->file('file'));
            $import = $import->data;

            return response()->json($import, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->file('file')) {
                $file = $request->file('file');
                $filename = $file->getClientOriginalName();
                $filename = str_replace(' ', '', $filename);

                $path = 'supplier_purchases';
                $full_path = storage_path()."/app/supplier_purchases/{$filename}";

                if (! Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->makeDirectory($path);
                }

                $storagePath = Storage::disk('local')->putFileAs('supplier_purchases', $file, $filename);

                $zip = Zip::open($full_path);
                $list = collect($zip->listFiles());

                $regex = preg_grep('/^.*\.(xml)$/i', $zip->listFiles());

                if (count($list) < 1 || count($regex) < 1) {
                    Storage::disk('local')->delete('supplier_purchases/'.$filename);

                    return response()->json([
                        'code' => '502',
                        'msg' => 'El archivo ZIP no es valido o no contiene documentos validos de la DIAN',
                    ], 500);
                }

                $keyXML = null;
                $keyPDF = null;

                foreach ($list as $key => $item) {
                    if (str_contains($item, '.pdf')) {
                        $keyPDF = $key;
                    } elseif (str_contains($item, '.xml')) {
                        $keyXML = $key;
                    }
                }

                $zip->extract(storage_path().'/app/supplier_purchases');
                Storage::disk('local')->delete("/app/supplier_purchases/{$filename}");

                if (Storage::disk('local')->exists($storagePath)) {
                    $xml = $this->readXMLv2(storage_path().'/app/supplier_purchases/'.$list[$keyXML]);

                    if ($xml['applicationResponse'] && array_key_exists('ResponseCode', $xml['applicationResponse']) && $xml['applicationResponse']['ResponseCode'] === '02') {
                        $exits_document = DB::table('supplier_purchases')
                            ->whereJsonContains('document_information', $xml['documentInformation']['UUID'])
                            ->count();

                        if ($exits_document > 0) {
                            throw new Exception(
                                "Documento electrónico
                                    <span class='font-bold'>{$xml['documentInformation']['ID']}</span>
                                    con UUID <span class='font-bold'>{$xml['documentInformation']['UUID']}</span> <br>
                                    <span class='font-bold text-danger'>ya se encontraba cargado a la plataforma </span>",
                                '401'
                            );
                        }

                        SupplierPurchase::create([
                            'application_response' => $xml['applicationResponse'],
                            'document_information' => $xml['documentInformation'],
                            'customer' => $xml['receiverParty'],
                            'supplier' => $xml['senderParty'],
                            'payment_means' => $xml['paymentMeans'],
                            'payment_terms' => $xml['paymentTerms'],
                            'allowance_charge' => $xml['allowanceCharge'],
                            'legal_monetary_total' => $xml['legalMonetaryTotal'],
                            'tax_total' => $xml['taxTotal'],
                            'items' => $xml['invoiceLines'],
                            'pdf_path' => $list[$keyPDF],
                            'xml_path' => $list[$keyXML],
                            'upload_by' => Auth::id(),
                            'state' => 'pending',
                            'work_center' => 'NA',
                            'entity' => 'CIEV',
                            'dian_state' => $xml['applicationResponse']['ResponseCode'],
                        ]);

                        DB::commit();

                        $supplier_purchases = SupplierPurchase::with('upload_user')
                            ->where('entity', '=', 'CIEV')
                            ->get();

                        return response()->json($supplier_purchases, 200);
                    } else {
                        return response()->json([
                            'code' => '502',
                            'msg' => 'XML no valido',
                        ], 500);
                    }
                } else {
                    DB::rollBack();

                    return response()->json([
                        'code' => '502',
                        'msg' => "Error saving files: {$full_path}",
                    ], 500);
                }
            } else {
                return response()->json([
                    'code' => '501',
                    'msg' => 'Error al procesar el documento',
                ], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param $file
     * @return \Illuminate\Http\Response|mixed
     * @throws BindingResolutionException
     */
    public function view_pdf($file){
        $document = file_get_contents(storage_path('app/supplier_purchases').'/'.$file);

        return response()->make($document, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
