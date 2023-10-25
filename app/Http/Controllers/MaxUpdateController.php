<?php

namespace App\Http\Controllers;

use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MaxUpdateController extends Controller
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
    public function purchase_orders(): Response
    {
        $orders = DB::connection($this->environment)
            ->table('CIEV_V_OrdenesCompra')
            ->select('OC', 'PROVEEDOR')
            ->where('ESTADO', '=', '3')
            ->groupBy('OC', 'PROVEEDOR')
            ->get();

        return Inertia::render('Applications/MaxUpdate/PurchaseOrder', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param $oc
     * @return JsonResponse
     */
    public function list_order($oc): JsonResponse
    {
        $order_list = DB::connection($this->environment)
            ->table('CIEV_V_OrdenesCompra')
            ->where('ESTADO', '=', '3')
            ->where('OC', '=', $oc)
            ->get()
            ->map(function ($row) {
                return [
                    'oc' => $row->OC,
                    'item' => $row->ITEM,
                    'line' => $row->LINEA,
                    'date' => $row->FECHA,
                    'reference' => $row->REFERENCIA,
                    'product' => $row->PRODUCTO,
                    'quantity' => number_format($row->CANTIDAD, 2, '.', ''),
                    'pending' => number_format($row->PENDIENTE, 2, '.', ''),
                    'price' => number_format($row->PRECIO, 2, '.', ''),
                    'total' => number_format($row->TOTAL, 2, '.', ''),
                    'cellar' => '',
                    'registry_quantity' => 0,
                    'invoice_reference' => '',
                    'selected_row' => false,
                    'available_lots' => DB::connection($this->environment)
                        ->table('Part_Lot')
                        ->where('PRTNUM_68', '=', trim($row->REFERENCIA))
                        ->get(),
                    'lots' => [],
                    'LOTTRK_01'=>DB::connection($this->environment)
                        ->table('Part_Master')
                        ->where('PRTNUM_01','=',trim($row->REFERENCIA))
                        ->select('LOTTRK_01')
                        ->get(),

                ];
            });

        return response()->json($order_list, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function receipt_purchase_order(Request $request): JsonResponse
    {
        $type = 'R';
        $subtype = 'P';
        $result = [];

        foreach ($request->orderList as $row) {
            try {
                $DOMDocumentXML = new DOMDocument();
                $DOMDocumentXML->preserveWhiteSpace = false;
                $DOMDocumentXML->formatOutput = true;
                $DOMDocumentXML->loadXML(view('templates.xml.transaction', compact('row', 'type', 'subtype'))->render());
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
                $result[] = $response->getBody()->getContents();
            } catch (ServerException|GuzzleException $e) {
                $result[] = $e->getMessage();
            }
        }

        $orders = DB::connection($this->environment)
            ->table('CIEV_V_OrdenesCompra')
            ->select('OC', 'PROVEEDOR', 'FECHA')
            ->where('ESTADO', '=', '3')
            ->groupBy('OC', 'PROVEEDOR', 'FECHA')
            ->get();

        return response()->json([
            'table' => $orders,
            'result' => $result,
        ], 200);
    }

    /**
     * @return Response
     */
    public function production_orders()
    {
        return Inertia::render('Applications/MaxUpdate/ProductionOrder');
    }

    /**
     * @param $order
     * @return JsonResponse
     */
    public function production_order_view($order)
    {
        try {
            $result = DB::connection($this->environment)
                ->table('CIEV_V_OP')
                ->where('STATUS_10', '=', '3')
                ->where('ORDNUM_10', '=', $order)
                ->first();

            if (! $result) {
                throw new Exception("La orden #$order no ha sido encontrada o no se encuentra abierta, por favor comuníquese con sistemas si cree que esto es un error", 500);
            }

            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function production_order_register(Request $request)
    {
        try {
            $ghosts = DB::connection($this->environment)
                ->table('Product_Structure')
                ->where('PARPRT_02', '=', $request->order['PRTNUM_10'])
                ->where('COMPRT_02', 'LIKE', 'FAN%')
                ->get();

            $result = [];

            if (count($ghosts) > 0) {
                $xmlGhostsList = [];
                foreach ($ghosts as $ghost) {
                    $ghost_cellar = DB::connection($this->environment)
                        ->table('Part_Master')
                        ->where('PRTNUM_01', '=', $ghost->COMPRT_02)
                        ->pluck('DELSTK_01')
                        ->first();

                    $ghost_parents = DB::connection($this->environment)
                        ->table('Product_Structure')
                        ->where('PARPRT_02', '=', $ghost->COMPRT_02)
                        ->get();

                    foreach ($ghost_parents as $ghost_parent) {
                        $transaction = [
                            'op' => $request->order_number,
                            'linum' => '00',
                            'delnum' => '00',
                            'product' => trim($ghost_parent->COMPRT_02),
                            'cellar' => $ghost_cellar,
                            'quantity' => number_format($ghost_parent->QTYPER_02 * $request->quantity, 10, ',', ''),
                        ];

                        $isXML = new DOMDocument();
                        $isXML->preserveWhiteSpace = false;
                        $isXML->formatOutput = true;
                        $isXML->loadXML(view('templates.xml.ProductionOrder.is', compact('transaction'))->render());

                        $is_xml = $isXML->saveXML();
                        $xmlGhostsList[] = base64_encode($is_xml);
                    }
                    $result[] = $this->send_transaction($xmlGhostsList);
                }
            }

            $rs = [
                'op' => $request->order_number,
                'quantity' => $request->quantity,
            ];

            $rsXML = new DOMDocument();
            $rsXML->preserveWhiteSpace = false;
            $rsXML->formatOutput = true;
            $rsXML->loadXML(view('templates.xml.ProductionOrder.rs', compact('rs'))->render());

            $rs_xml = $rsXML->saveXML();
            $rs_xml = base64_encode($rs_xml);
            $result[] = $this->send_transaction([$rs_xml]);

            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        } catch (GuzzleException $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @throws GuzzleException
     */
    protected function send_transaction($xmlList)
    {
        try {
            $client = new Client(['base_uri' => "http://192.168.1.42:9200/api/v1/$this->company/transaction"]);
            $response = $client->request('POST', "http://192.168.1.42:9200/api/v1/$this->company/transaction", [
                'headers' => [
                    'Accept' => '*/*',
                    'Content-type' => 'application/json',
                ],
                'json' => [
                    'ListXML' => $xmlList,
                ],
            ]);

            return $response->getBody()->getContents();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
