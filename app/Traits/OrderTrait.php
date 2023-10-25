<?php

namespace App\Traits;

use App\Models\HeaderOrder;
use Carbon\Carbon;
use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;

trait OrderTrait
{
    /**
     * @param $id
     * @param $dies_reason
     * @param $type
     * @param $point_of_sale_reason
     * @return array
     *
     * @throws GuzzleException
     */
    protected function generate_order($id, $type, $point_of_sale_reason = null, $dies_reason = null): array
    {
        //order
        $order = HeaderOrder::with('customer', 'details')->find($id);

        if ($type == 'forecast') {
            return $this->CreateForecastOrder($order);
        }

        //notes
        $notes = $this->divide_notes($order->notes);

        //type
        $stype = $type == 'forecast' ? 'FC' : 'CU';

        //reason
        $reason = '';
        switch ($type) {
            case 'national':
                $reason = $order->destiny == 'C' ? '23' : ($order->destiny == 'P' ? '22' : $dies_reason);
                break;
            case 'export':
                $reason = '27';
                break;
            case 'samples':
                $reason = '20';
                break;
            case 'elena':
                $reason = '26';
                break;
            case 'point_of_sale':
                $reason = $point_of_sale_reason;
                break;
            case 'services':
                $reason = '24';
                break;
            case 'delivered_merchandise':
                $reason = '07';
                break;
            case 'claim':
                $reason = '23';
                break;
        }

        //currency
        $curr = $type === 'export' ? 'USD' : trim($order->customer_master->CURR_23);

        // exchange
        $excrte = $type === 'export'
            ? DB::connection('MAX')
                ->table('Code_Master')
                ->where('CDEKEY_36', '=', 'CURR')
                ->where('CODE_36', '=', 'US')
                ->pluck('EXCRTE_36')
                ->first()
            : 1;

        //Header XML
        $header_xml = new DOMDocument();
        $header_xml->preserveWhiteSpace = false;
        $header_xml->formatOutput = true;
        $header_xml->loadXML(
            view('templates.xml.SalesOrder.header',
                compact('order', 'notes', 'stype', 'reason', 'curr', 'excrte')
            )->render()
        );
        $header_xml = $header_xml->saveXML();
        $header_xml = base64_encode($header_xml);

        //guzzle client
        $client = new Client(['base_uri' => 'http://192.168.1.42:9200/api/v1/create-sales-order-header']);

        //response
        $response = $client->request('POST', 'http://192.168.1.42:9200/api/v1/create-sales-order-header', [
            'headers' => ['Accept' => '*/*', 'Content-type' => 'application/json'],
            'json' => ['xml' => $header_xml],
        ]);
        $response_header = $response->getBody()->getContents();
        $response_header = json_decode($response_header);

        /**
         * DETAILS
         */

        //Detail Transaction Result
        $detail_transaction_result = [];

        // Send detail order
        if ($response_header->Code === 200) {
            foreach ($order->details as $idx => $item) {
                $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);

                $prtnum = DB::connection('MAX')
                    ->table('Part_Master')
                    ->where('PRTNUM_01', '=', $item->product)
                    ->first();

                $cellar = DB::connection('MAX')
                    ->table('Part_Sales')
                    ->where('PRTNUM_29', '=', $item->product)
                    ->pluck('STK_29')
                    ->first();

                $delivery_date = calculateDelivery($prtnum->MFGLT_01);

                // Detail XML
                $detail_xml = new DOMDocument();
                $detail_xml->preserveWhiteSpace = false;
                $detail_xml->formatOutput = true;
                $detail_xml->loadXML(view('templates.xml.SalesOrder.detail',
                    compact('order', 'item', 'idx', 'linum', 'prtnum', 'delivery_date', 'cellar', 'response_header', 'stype'))
                    ->render()
                );
                $detail_xml = $detail_xml->saveXML();
                $detail_xml = base64_encode($detail_xml);

                \Log::error("HEADER_$id: $header_xml");
                \Log::error("DETAIL_$id: $detail_xml");

                $client = new Client(['base_uri' => 'http://192.168.1.42:9200/api/v1/create-sales-order-detail']);
                $response = $client->request('POST', 'http://192.168.1.42:9200/api/v1/create-sales-order-detail', [
                    'headers' => ['Accept' => '*/*', 'Content-type' => 'application/json'],
                    'json' => ['xml' => $detail_xml],
                ]);

                //store result transactions
                $result = json_decode($response->getBody()->getContents());
                $detail_transaction_result[] = $result->Message;

                //store Extended fields
                if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                    DB::connection('MAX')
                        ->table('SO_Detail_Ext')
                        ->updateOrInsert([
                            'ORDER_LIN_DEL' => $response_header->Message.$linum.'01',
                        ], [
                            'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                            'CodProdCliente' => $item->customer_product_code,
                            'Marca' => $item->brand,
                        ]);
                }

                if ($item->notes) {
                    if (strlen($item->notes) <= 50) {
                        DB::connection('MAX')
                            ->table('SO_Note')
                            ->insert([
                                'ORDNUM_30' => $response_header->Message,
                                'LINNUM_30' => $linum,
                                'DELNUM_30' => '01',
                                'COMNUM_30' => '01',
                                'CODE_30' => 'B',
                                'COMNT_30' => $item->notes,
                                'CUSTID_30' => '',
                                'PIDCOD_30' => '',
                                'MCOMP_30' => '',
                                'MSITE_30' => '',
                                'UDFKEY_30' => '',
                                'UDFREF_30' => '',
                                'XDFINT_30' => 0,
                                'XDFFLT_30' => 0,
                                'XDFBOL_30' => '',
                                'XDFDTE_30' => null,
                                'XDFTXT_30' => '',
                                'FILLER_30' => '',
                                'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                                'CreationDate' => Carbon::now(),
                                'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                                'ModificationDate' => Carbon::now(),
                                'RECTYP_30' => 'ST',
                            ]);
                    } else {
                        $notes = str_split($item->notes, 50);

                        foreach ($notes as $key => $note) {
                            $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                            DB::connection('MAX')
                                ->table('SO_Note')
                                ->insert([
                                    'ORDNUM_30' => $response_header->Message,
                                    'LINNUM_30' => $linum,
                                    'DELNUM_30' => '01',
                                    'COMNUM_30' => $delnum,
                                    'CODE_30' => 'B',
                                    'COMNT_30' => $note,
                                    'CUSTID_30' => '',
                                    'PIDCOD_30' => '',
                                    'MCOMP_30' => '',
                                    'MSITE_30' => '',
                                    'UDFKEY_30' => '',
                                    'UDFREF_30' => '',
                                    'XDFINT_30' => 0,
                                    'XDFFLT_30' => 0,
                                    'XDFBOL_30' => '',
                                    'XDFDTE_30' => null,
                                    'XDFTXT_30' => '',
                                    'FILLER_30' => '',
                                    'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                                    'CreationDate' => Carbon::now(),
                                    'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                                    'ModificationDate' => Carbon::now(),
                                    'RECTYP_30' => 'ST',
                                ]);
                        }
                    }
                }
            }
        }

        return [
            'order' => $response_header->Message,
            'detail_transaction' => $detail_transaction_result,
        ];
    }

    /**
     * @param $order
     * @return array
     *
     * @throws GuzzleException
     */
    protected function CreateForecastOrder($order): array
    {
        $transaction_result = [];
        $orders = [];

        foreach ($order->details as $idx => $item) {
            $prtnum = DB::connection('MAX')
                ->table('Part_Master')
                ->where('PRTNUM_01', '=', $item->product)
                ->first();

            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);
            $delivery_date = Carbon::parse($delivery_date->DateValue)->format('ymd');

            $client = new Client(['base_uri' => 'http://192.168.1.42:9200/api/v1/create-forecast-order']);
            $response = $client->request('POST', 'http://192.168.1.42:9200/api/v1/create-forecast-order', [
                'headers' => ['Accept' => '*/*', 'Content-type' => 'application/json'],
                'json' => [
                    'partid' => $item->product,
                    'curdue' => $delivery_date,
                    'curqty' => floatval($item->quantity),
                    'ordref' => "{$order->id}{$idx}",
                ],
            ]);
            $result = json_decode($response->getBody()->getContents());

            if ($result->Code == 200) {
                DB::connection('MAX')
                    ->table('Order_Master')
                    ->where('ORDREF_10', '=', "{$order->id}{$idx}")
                    ->update([
                        'UDFKEY_10' => $order->customer_code,
                        'UDFREF_10' => "$item->art $item->brand",
                        'ORDREF_10' => substr('STOCK '.$order->customer->RAZON_SOCIAL, 0, 18),
                        'XDFTXT_10' => "{$order->id}{$idx}",
                    ]);

                $order_num = DB::connection('MAX')
                    ->table('Order_Master')
                    ->where('XDFTXT_10', '=', "{$order->id}{$idx}")
                    ->first();

                $transaction_result[] = $result->Message;
                $orders[] = $order_num->ORDNUM_10;
            } else {
                $transaction_result[] = "ERROR: {$order->id}{$idx} - $item->product";
            }
        }

        return [
            'order' => implode(', ', $orders),
            'detail_transaction' => $transaction_result,
        ];
    }

    /**
     * @param $days
     * @return object
     */
    protected function calculate_delivery($days): object
    {
        $business_days = DB::connection('MAX')
            ->table('Shop_Calendar')
            ->where('ShopDay', '=', 1)
            ->whereDate('DateValue', '>=', Carbon::now())
            ->get();

        if ($days > 0) {
            return $business_days[$days - 1];
        } else {
            $date = Carbon::now()->format('Y-m-d h:m:i');

            return (object) [
                'DateValue' => $date,
            ];
        }
    }

    /**
     * @param $notes
     * @return array
     */
    protected function divide_notes($notes): array
    {
        $note_1 = '';
        $note_2 = '';
        $note_3 = '';
        $notes_header = strlen($notes) >= 30 ? str_split($notes, 30) : $notes;

        if (is_array($notes_header)) {
            if (array_key_exists(0, $notes_header)) {
                $note_1 = $notes_header[0];
            }
            if (array_key_exists(1, $notes_header)) {
                $note_2 = $notes_header[1];
            }
            if (array_key_exists(2, $notes_header)) {
                $note_3 = $notes_header[2];
            }
        }

        return [
            'note_1' => $note_1,
            'note_2' => $note_2,
            'note_3' => $note_3,
        ];
    }
}
