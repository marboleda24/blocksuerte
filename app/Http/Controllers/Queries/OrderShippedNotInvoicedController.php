<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderShippedNotInvoicedController extends Controller
{
    /**
     * Permissions check
     */
    public function __construct()
    {
        $this->middleware('permission:queries.order-shipped-not-invoiced');
    }

    /**
     * @return Response
     */
    public function index()
    {
        $orders = DB::connection('MAX')
            ->table('CIEV_V_OV_ENV_NO_FACT_DNL')
            ->get();

        return Inertia::render('Applications/Queries/OrderShippedNotInvoiced', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param $order_number
     * @return JsonResponse
     */
    public function show($order_number)
    {
        $header = DB::connection('MAX')
            ->table('CIEV_V_OV_ENV_NO_FACT_DNL')
            ->where('order_number', '=', $order_number)
            ->first();

        $details = DB::connection('MAX')
            ->table('CIEV_V_OV_ENV_NO_FACT_DET_DNL')
            ->where('order_number', '=', $order_number)
            ->get();

        return response()->json([
            'header' => $header,
            'details' => $details,
        ], 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function close_order(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Invoice_Master')
                ->where('INVCE_31', '=', '')
                ->where('ORDNUM_31', '=', $request->order_number)
                ->delete();

            DB::connection('MAX')
                ->table('SO_Detail')
                ->where('ORDNUM_28', '=', $request->order_number)
                ->where('status_28', '=', 3)
                ->update([
                    'STATUS_28' => 4,
                    'ModifiedBy' => 'EVPIU-'.Auth::user()->username,
                ]);

            DB::connection('MAX')
                ->table('SO_Master')
                ->where('ORDNUM_27', '=', $request->order_number)
                ->where('STATUS_27', '=', 3)
                ->update([
                    'STATUS_27' => 4,
                    'ModifiedBy' => 'EVPIU-'.Auth::user()->username,
                ]);

            DB::connection('MAX')
                ->table('Order_Master')
                ->where('ORDNUM_10', '=', $request->order_number)
                ->where('STATUS_10', '=', 3)
                ->update([
                    'STATUS_10' => 4,
                    'ModifiedBy' => 'EVPIU-'.Auth::user()->username,
                ]);

            DB::connection('MAX')
                ->table('Requirement_Detail')
                ->where('ORDNUM_11', '=', $request->order_number)
                ->where('STATUS_11', '=', 3)
                ->update([
                    'STATUS_11' => 4,
                    'ModifiedBy' => 'EVPIU-'.Auth::user()->username,
                ]);

            DB::connection('MAX')->commit();

            $orders = DB::connection('MAX')
                ->table('CIEV_V_OV_ENV_NO_FACT_DNL')
                ->get();

            return response()->json($orders, 200);
        } catch (\Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
