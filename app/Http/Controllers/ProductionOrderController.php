<?php

namespace App\Http\Controllers;

use App\Models\MAX\OrderMaster;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductionOrderController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/ProductionOrder/Index');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_data(Request $request): JsonResponse
    {
        try {
            $result = [];
            switch ($request->type) {
                case 'number':
                    $result = $this->search_by_number($request->number);
                    break;
                case 'date':
                    $result = $this->search_by_date($request->date);
                    break;
                case 'product':
                    $result = $this->search_by_product($request->product);
                    break;
            }

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $date
     * @return Collection
     */
    protected function search_by_date($date): Collection
    {
        $date = explode(' - ', $date);

        return OrderMaster::with('production_batch')
            ->whereBetween('FechaCreacion', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
            ->get()->map(function ($row) {
                return [
                    'order' => $row->ORDNUM_10,
                    'code' => trim($row->PRTNUM_10),
                    'description' => trim($row->PMDES1_01),
                    'quantity' => $row->CURQTY_10,
                    'sync' => (bool) $row->production_batch,
                ];
            });
    }

    /**
     * @param $number
     * @return Collection
     */
    protected function search_by_number($number): Collection
    {
        return OrderMaster::with('production_batch')
            ->where('ORDNUM_10', '=', $number)
            ->get()->map(function ($row) {
                return [
                    'order' => $row->ORDNUM_10,
                    'code' => trim($row->PRTNUM_10),
                    'description' => trim($row->PMDES1_01),
                    'quantity' => $row->CURQTY_10,
                    'sync' => (bool) $row->production_batch,
                ];
            });
    }

    /**
     * @param $product
     * @return Collection
     */
    protected function search_by_product($product): Collection
    {
        return OrderMaster::with('production_batch')
            ->where('PRTNUM_10', '=', $product)
            ->get()->map(function ($row) {
                return [
                    'order' => $row->ORDNUM_10,
                    'code' => trim($row->PRTNUM_10),
                    'description' => trim($row->PMDES1_01),
                    'quantity' => $row->CURQTY_10,
                    'sync' => (bool) $row->production_batch,
                ];
            });
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function order_modal(Request $request)
    {
        try {
            $data=DB::connection('MAX')
                ->table('CIEV_V_EstadoOP')
                ->where('ORDNUM_14', '=',$request->order)
                ->orderBy('OPRSEQ_14')
                ->get()->map(function ($row) {
                    return[
                        'CTActual' => $row->CTActual,
                        'OPRDES_14' => $row->OPRDES_14,
                        'WRKCTR_14' => $row->WRKCTR_14,
                        'QTYREM_14' => $row->QTYREM_14,
                        'QTYCOM_14' => $row->QTYCOM_14,
                            'OV' => $row->OV,
                        'MOVDTE_14' => $row->MOVDTE_14,
                        'PMDES1_01' => $row->PMDES1_01,
                        'STATUS_10' => $row->STATUS_10,
                        'ORDNUM_14' => $row->ORDNUM_14,
                        'OPRSEQ_14' => $row->OPRSEQ_14
                    ];
                });

            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

}
