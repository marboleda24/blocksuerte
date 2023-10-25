<?php

namespace App\Http\Controllers\Max;

use App\Http\Controllers\Controller;
use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InventoryIssueController extends Controller
{
    public string $environment;
    public string $company;


    public function __construct()
    {
        $this->environment = config('app.max_update_environment') === 'production'
            ? 'MAX'
            : 'MAXP';

        $this->company = config('app.max_update_environment');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $products = DB::connection($this->environment)
            ->table('Part_Master')
            ->select('PRTNUM_01', 'PMDES1_01', 'ONHAND_01 as QTY', 'MAXQTY_01 as MAX', 'MINQTY_01 as MIN', 'BOMUOM_01')
            ->where('COMCDE_01', '=', 'QUIMICOS')
            ->where('ONHAND_01', '>', 0)
            ->get();

        return Inertia::render('Applications/MaxUpdate/InventoryIssue', [
            'products' => $products,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $DOMDocumentXML = new DOMDocument();
            $DOMDocumentXML->preserveWhiteSpace = false;
            $DOMDocumentXML->formatOutput = true;
            $DOMDocumentXML->loadXML(view('templates.xml.InventoryIssue', compact('request'))->render());
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

            $response = $response->getBody()->getContents();

            $products = DB::connection($this->environment)
                ->table('Part_Master')
                ->select('PRTNUM_01', 'PMDES1_01', 'ONHAND_01 as QTY', 'MAXQTY_01 as MAX', 'MINQTY_01 as MIN', 'BOMUOM_01')
                ->where('COMCDE_01', '=', 'QUIMICOS')
                ->get();

            return response()->json([
                'products' => $products,
                'result' => $response,
            ], 200);
        } catch (Exception $e) {
            return response()->json(mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8'), 500);
        }
    }

    /**
     * @param $prtnum
     * @return JsonResponse
     */
    public function get_lots($prtnum): JsonResponse
    {
        $query = DB::connection($this->environment)
            ->table('Part_Lot')
            ->where('PRTNUM_68', '=', $prtnum)
            ->where('QTYOH_68', '>', 0)
            ->get()->map(function ($row) {
                return [
                    'lot' => trim($row->LOTNUM_68),
                    'qty' => 0,
                    'stock' => floatval($row->QTYOH_68)
                ];
            });

        return response()->json($query, 200);
    }
}
