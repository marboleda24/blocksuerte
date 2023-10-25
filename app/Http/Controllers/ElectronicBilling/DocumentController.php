<?php

namespace App\Http\Controllers\ElectronicBilling;

use App\Http\Controllers\Controller;
use App\Models\Dian\ApiDocument;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use PHPUnit\Runner\Exception;

class DocumentController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function index($entity): Response
    {
        return Inertia::render('Applications/ElectronicBilling/Document', [
            'entity' => $entity
        ]);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function search_customer(Request $request, $entity): JsonResponse
    {
        $query = $request->get('q');

        $connection = $entity === 'CIEV' ? 'MAX' : 'MAXPG';
        $table = $entity === 'CIEV' ? 'CIEV_V_Clientes' : 'PG_V_Clientes';

        $queries = Auth::user()->hasRole('super-admin')
            ? DB::connection($connection)
                ->table($table)
                ->where('RAZON_SOCIAL', 'like', "%{$query}%")
                ->orWhere('CODIGO_CLIENTE', 'like', "%{$query}%")
                ->orWhere('NIT', 'like', "%{$query}%")
                ->take(20)
                ->get()
                ->toArray()
            : DB::connection($connection)
                ->table($table)
                ->where('VENDEDOR', '=', auth()->user()->vendor_code)
                ->where('RAZON_SOCIAL', 'LIKE', "%{$query}%")
                ->orWhere('CODIGO_CLIENTE', 'LIKE', "%{$query}%")
                ->orWhere('NIT', 'like', "%{$query}%")
                ->take(20)
                ->get()
                ->toArray();

        $queries = array_map(function ($value) {
            return [
                'nit' => str_contains($value->NIT, '-') ? substr($value->NIT, 0, strpos($value->NIT, '-')) : $value->NIT,
                'code' => $value->CODIGO_CLIENTE,
                'customer' => $value->RAZON_SOCIAL,
            ];
        }, $queries);

        return response()->json($queries, 200);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function get_documents(Request $request, $entity): JsonResponse
    {

        if ($request->type === 'customer') {
            $documents = ApiDocument::select('number', 'prefix', 'created_at', 'request_api', 'cufe', 'pdf', 'xml', 'client', 'prefix', 'type_document_id')
                ->where('state_document_id', '=', 1)
                ->where('identification_number', '=', $entity === 'CIEV' ? 890926617 : 900349726)
                ->where('customer', '=', $request->nit)
                ->orderBy('number', 'desc')
                ->get();
        } else {
            $documents = ApiDocument::select('number', 'prefix', 'created_at', 'request_api', 'cufe', 'pdf', 'xml', 'client', 'prefix', 'type_document_id')
                ->where('state_document_id', '=', 1)
                ->where('identification_number', '=', $entity === 'CIEV' ? 890926617 : 900349726)
                ->where('number', 'like', "%$request->invoice%")
                ->orderBy('number', 'desc')
                ->get();
        }

        $documents = $documents->map(function ($document) {
            return (object)[
                'document' => $document['number'],
                'number' => "{$document['prefix']}-{$document['number']}",
                'date' => $document['created_at'],
                'date_invoice' => json_decode($document['request_api'])->date,
                'cufe' => $document['cufe'],
                'file_pdf' => $document['pdf'],
                'file_ubl' => $document['xml'],
                'client' => $document['client'],
                'prefix' => $document['prefix'],
                'type_document' => $document['type_document_id'] === '1' ? 'Factura Nacional' : ($document['type_document_id'] === '2' ? 'Factura de Exportación' : 'Nota Crédito'),
            ];
        })->unique('number')->values()->all();

        return response()->json($documents, 200);
    }

    /**
     * @param $entity
     * @return JsonResponse
     */
    public function validateConsecutive($entity): JsonResponse
    {
        $documents = DB::connection('API_DIAN')
            ->table('documents')
            ->select('number', 'request_api')
            ->where('identification_number', '=', $entity === 'CIEV' ? 890926617 : 900349726)
            ->whereIn('type_document_id', [1, 2])
            ->where('state_document_id', '=', 1)
            ->orderBy('number', 'asc')
            ->distinct()
            ->get()
            ->map(function ($row) {
                return [
                    'number' => (int)$row->number,
                    'date' => Carbon::parse(json_decode($row->request_api)->date)->format('Y-m-d'),
                ];
            });

        $documents = $documents->where('date', '>', Carbon::now()->subDays(15))
            ->map(function ($row) {
                return $row['number'];
            })->toArray();

        $arr2 = range(min($documents), max($documents));
        $missing = array_values(array_diff($arr2, $documents));

        return response()->json($missing);
    }

    /**
     * @param $entity
     * @return JsonResponse
     */
    public function validateConsecutiveNC($entity): JsonResponse
    {
        $data = DB::connection('API_DIAN')
            ->table('documents')
            ->whereDate('created_at', '>', Carbon::now()->subMonth()->format('Y-m-d 00:00:00'))
            ->whereIn('type_document_id', [5])
            ->where('state_document_id', '=', 1)
            ->orderBy('number', 'asc')
            ->get()->map(function ($row) {
                return (int)$row->number;
            })->toArray();

        $arr2 = range(min($data), max($data));
        $missing = array_values(array_diff($arr2, $data));

        return response()->json($missing);
    }

    /**
     * @param $document
     * @param $entity
     * @return string
     * @throws GuzzleException
     */
    public function download($entity, $document)
    {
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);
        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');
        $nit = $entity === 'CIEV' ? 890926617 : 900349726;

        $response = $client->request('GET', "$url/invoice/$nit/$document", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ],
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param Request $request
     * @param $entity
     * @return string
     * @throws GuzzleException
     */
    public function regenerate(Request $request, $entity)
    {
        try {
            $url = config('apidian.url');
            $client = new Client(['base_uri' => $url]);
            $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');

            $response = $client->request('GET', "$url/regenerate-pdf", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ],
                'json' => [
                    'company' => $request->company,
                    'document' => $request->document,
                ],
            ]);

            return $response->getBody()->getContents();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @param $entity
     * @return string
     * @throws GuzzleException
     */
    public function send_mail_invoice_dian(Request $request, $entity)
    {
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);
        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');

        $response = $client->request('POST', "$url/ubl2.1/send-email", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ],
            'json' => [
                'alternate_email' => $request->email,
                'prefix' => $request->prefix,
                'number' => strval($request->number),
            ],
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $company
     * @param $prefix
     * @param $document
     * @param $entity
     * @return string
     * @throws GuzzleException
     */
    public function download_invoice_dian($company, $prefix, $document, $entity)
    {
        $url = config('apidian.url');
        $client = new Client(['base_uri' => $url]);
        $token = config($entity === 'CIEV' ? 'apidian.token' : 'apidian.token_goja');

        $response = $client->request('GET', "$url/invoice/$company/{$prefix}{$document}.pdf", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ],
        ]);

        return $response->getBody()->getContents();
    }
}
