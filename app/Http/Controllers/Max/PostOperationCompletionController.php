<?php

namespace App\Http\Controllers\Max;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceAsset;
use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Http\Exception\ClientException;
use PHPUnit\Runner\Exception;

class PostOperationCompletionController extends Controller
{
    public string $environment;
    public string $company;

    public function __construct()
    {
        $this->environment = config('app.max_update_environment') === 'production'
            ? 'MAX'
            : 'MAX';

        $this->company = config('app.max_update_environment');
    }

    /**
     * @return Response
     */
    public function index(){
        $orders = DB::connection('MAX')
            ->table('CIEV_V_EstadoOP')
            ->where('STATUS_10', '=', '3')
            ->where('CTActual', '=', 'Y')
            ->where('QTYREM_14', '>', '0')
            ->orderBy('ORDNUM_14')
            ->get();

        $assets = MaintenanceAsset::where('state', '=', 'good')
            ->get();

        return Inertia::render('Applications/MaxUpdate/PostOperationCompletion', [
            'orders' => $orders,
            'assets' => $assets
        ]);
    }


    /**
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function update_operation(Request $request){
        try {
            $DOMDocumentXML = new DOMDocument();
            $DOMDocumentXML->preserveWhiteSpace = false;
            $DOMDocumentXML->formatOutput = true;
            $DOMDocumentXML->loadXML(view('templates.xml.PostOperationCompletion', compact('request'))->render());
            $xml = $DOMDocumentXML->saveXML();
            $xml = base64_encode($xml);

            $client = new Client(['base_uri' => "http://192.168.1.42:9200/api/v1/$this->company/transaction"]);

            $headers = [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
            ];

            $params = ['xml' => $xml];

            $response = $client->request('POST', "http://192.168.1.42:9200/api/v1/$this->company/transaction", [
                'headers' => $headers,
                'json' => $params,
            ]);

            $response = json_decode($response->getBody()->getContents());

            $orders = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('STATUS_10', '=', '3')
                ->where('CTActual', '=', 'Y')
                ->where('QTYREM_14', '>', '0')
                ->orderBy('ORDNUM_14')
                ->get();

            return response()->json([
                'orders' => $orders,
                'result' => $response
            ], 200);
        }catch (ServerException $e){
            $response = $e->getResponse();
            return response()->json($response->getReasonPhrase(), 500);
        }

    }
}
