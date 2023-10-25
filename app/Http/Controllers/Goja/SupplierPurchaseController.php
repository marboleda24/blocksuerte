<?php

namespace App\Http\Controllers\Goja;

use App\Custom\ReadXMLTrait;
use App\Http\Controllers\Controller;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZanySoft\Zip\Facades\Zip;

class SupplierPurchaseController extends Controller
{
    use ReadXMLTrait;

    /**
     * @return Response
     */
    public function index()
    {
        $supplier_purchases = SupplierPurchase::with('upload_user')
            ->where('entity', '=', 'GOJA')
            ->get();

        return Inertia::render('Applications/Goja/SupplierPurchase', [
            'supplier_purchases' => $supplier_purchases,
        ]);
    }

    /**
     * @throws Exception
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

                if (count($list) < 1 || ! in_array($regex[0] ?? $regex['1'], $zip->listFiles())) {
                    Storage::disk('local')->delete('supplier_purchases/'.$filename);

                    return response()->json([
                        'code' => '502',
                        'msg' => 'El archivo ZIP no es valido o no contiene documentos validos de la DIAN',
                    ], 500);
                }

                $zip->extract(storage_path().'/app/supplier_purchases');
                Storage::disk('local')->delete("/app/supplier_purchases/{$filename}");

                if (Storage::disk('local')->exists($storagePath)) {
                    $xml = $this->readDocument(storage_path().'/app/supplier_purchases/'.(str_contains($list[1], '.xml') ? $list[1] : $list[0]));

                    if ($xml['ApplicationResponse'] && array_key_exists('ResponseCode', $xml['ApplicationResponse']) && $xml['ApplicationResponse']['ResponseCode'] === '02') {
                        $exits_document = DB::table('supplier_purchases')
                            ->whereJsonContains('document_information', $xml['DocumentInformation']['UUID'])
                            ->count();

                        if ($exits_document > 0) {
                            throw new Exception(
                                "Documento electrónico
                                    <span class='font-bold'>{$xml['DocumentInformation']['ID']}</span>
                                    con UUID <span class='font-bold'>{$xml['DocumentInformation']['UUID']}</span> <br>
                                    <span class='font-bold text-danger'>ya se encontraba cargado a la plataforma </span>",
                                '401'
                            );
                        }

                        SupplierPurchase::create([
                            'application_response' => $xml['ApplicationResponse'],
                            'document_information' => $xml['DocumentInformation'],
                            'customer' => $xml['Customer'],
                            'supplier' => $xml['Supplier'],
                            'payment_means' => $xml['PaymentMeans'],
                            'payment_terms' => $xml['PaymentTerms'],
                            'allowance_charge' => $xml['AllowanceCharge'],
                            'legal_monetary_total' => $xml['LegalMonetaryTotal'],
                            'tax_total' => $xml['TaxTotal'],
                            'items' => $xml['Items'],
                            'pdf_path' => (str_contains($list[0], '.pdf') ? $list[0] : $list[1]),
                            'xml_path' => (str_contains($list[0], '.xml') ? $list[0] : $list[1]),
                            'upload_by' => Auth::id(),
                            'state' => 'pending',
                            'work_center' => 'NA',
                            'entity' => 'GOJA',
                            'dian_state' => $xml['ApplicationResponse']['ResponseCode'],
                        ]);

                        DB::commit();

                        $supplier_purchases = SupplierPurchase::with('upload_user')
                            ->where('entity', '=', 'GOJA')
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
                    'identification_number' => 900349726,
                    'dv' => 2,
                    'name' => 'PLASTICOS GOJA S.A.S',
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

            $token = config('apidian.token_goja');
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
                $document->dian_state = $request->state;
                $request->state === '032'
                    ? $document->received_by = Auth::id()
                    : $document->accepted_by = Auth::id();
                $document->save();
                DB::commit();

                $supplier_purchases = SupplierPurchase::with('upload_user')
                    ->where('entity', '=', 'GOJA')
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
                    'prefix' => $document->document_information['Prefix'],
                    'uuid' => $document->document_information['UUID'],
                    'type_document_id' => 1,
                ],
            ];

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
                    ->where('entity', '=', 'GOJA')
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
     * @return Response
     */
    public function work_center()
    {
        $user_works = \auth()->user()->work_center_array;

        $supplier_purchases = SupplierPurchase::with('upload_user', 'received_user', 'accepted_user')
            ->where('entity', '=', 'GOJA')
            ->whereIn('dian_state', ['030', '032', '033'])
            ->whereIn('work_center', $user_works)
            ->get();

        return Inertia::render('Applications/SupplierPurchase/WorkCenter', [
            'supplier_purchases' => $supplier_purchases,
        ]);
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
