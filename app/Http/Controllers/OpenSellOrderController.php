<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OpenSellOrderController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function index($entity)
    {
        if ($entity === 'CIEV') {
            if (auth()->user()->hasRole('super-admin') || auth()->user()->can('queries.open-sell-orders.show-all')) {
                $query = DB::connection('MAX')
                    ->table('CIEV_V_OV_ABIERTAS_DNL')
                    ->orderBy('RAZON_SOCIAL')
                    ->get()
                    ->groupBy('RAZON_SOCIAL');
            } else {
                $query = DB::connection('MAX')
                    ->table('CIEV_V_OV_ABIERTAS_DNL')
                    ->where('COD_VENDEDOR', '=', auth()->user()->vendor_code)
                    ->get()
                    ->groupBy('RAZON_SOCIAL');
            }

        } else{
            $query = DB::connection('MAXPG')
                ->table('PG_V_OV_ABIERTAS_DNL')
                ->orderBy('RAZON_SOCIAL')
                ->get()
                ->groupBy('RAZON_SOCIAL');
        }

        return Inertia::render('Applications/Queries/OpenSellOrders', [
            'query' => $query,
            'entity' => $entity
        ]);
    }

    /**
     * @param $ov
     * @param $entity
     * @return JsonResponse
     */
    public function show_ov($entity, $ov)
    {
        if ($entity === 'CIEV'){
            try {
                $manufacture_orders = DB::connection('MAX')
                    ->table('Order_Master as OM')
                    ->join('Part_Master as PM', 'OM.PRTNUM_10', '=', 'PM.PRTNUM_01')
                    ->select('ORDNUM_10', 'PRTNUM_10', 'PMDES1_01', 'PMDES2_01', 'STATUS_10')
                    ->where('OM.ORDNUM_10', 'LIKE', '5%')
                    ->where('OM.ORDREF_10', 'LIKE', "$ov%")
                    ->get()->map(function ($row) {
                        $row->detail = DB::connection('MAX')
                            ->table('Job_Progress')
                            ->where('ORDNUM_14', '=', "{$row->ORDNUM_10}0000")
                            ->orderBy('OPRSEQ_14')
                            ->get();

                        return $row;
                    });

                $invoices = DB::connection('MAX')
                    ->table('CIEV_V_FacturasDetalladas')
                    ->select('Factura', 'CodigoProducto', 'DescripcionProducto', 'FechaFacturacion', 'Cantidad', 'Precio', 'TotalItem')
                    ->where('OV', '=', substr($ov, '0', '8'))
                    ->where('Item', '=', substr($ov, '8', '4'))
                    ->get();

                return response()->json([
                    'manufacture_orders' => $manufacture_orders,
                    'invoices' => $invoices,
                ], 200);
            } catch (Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }else {
            try {
                $manufacture_orders = DB::connection('MAXPG')
                    ->table('Order_Master as OM')
                    ->join('Part_Master as PM', 'OM.PRTNUM_10', '=', 'PM.PRTNUM_01')
                    ->select('ORDNUM_10', 'PRTNUM_10', 'PMDES1_01', 'PMDES2_01', 'STATUS_10')
                    ->where('OM.ORDNUM_10', 'LIKE', '5%')
                    ->where('OM.ORDREF_10', 'LIKE', "$ov%")
                    ->get()->map(function ($row) {
                        $row->detail = DB::connection('MAX')
                            ->table('Job_Progress')
                            ->where('ORDNUM_14', '=', "{$row->ORDNUM_10}0000")
                            ->orderBy('OPRSEQ_14')
                            ->get();

                        return $row;
                    });

                $invoices = DB::connection('MAXPG')
                    ->table('PG_V_FacturasDetalladas')
                    ->select('Factura', 'CodigoProducto', 'DescripcionProducto', 'FechaFacturacion', 'Cantidad', 'Precio', 'TotalItem')
                    ->where('OV', '=', substr($ov, '0', '8'))
                    ->where('Item', '=', substr($ov, '8', '4'))
                    ->get();

                return response()->json([
                    'manufacture_orders' => $manufacture_orders,
                    'invoices' => $invoices,
                ], 200);
            } catch (Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }
    }
}
