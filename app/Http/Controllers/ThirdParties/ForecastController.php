<?php

namespace App\Http\Controllers\ThirdParties;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ForecastController extends Controller
{
    /**
     * ForecastController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:application.third-parties.forecasts');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = DB::connection('MAX')->table('CIEV_V_Pronosticos')
            ->where('estado', '=', '3')
            ->orderBy('estado', 'desc')
            ->get();

        return Inertia::render('Applications/ThirdParties/Forecasts', [
            'table_data' => $data,
        ]);
    }

    /**
     * get_data
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_data(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_Pronosticos')
                ->where('estado', '=', $request->state)
                ->orderBy('estado', 'desc')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * query_op
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $forecasts = DB::connection('MAX')
                ->table('CIEV_V_OP_Pronosticos_v1')
                ->where('pronostico', '=', $request->forecast)
                ->get();

            foreach ($forecasts as $forecast) {
                $forecast->orders = DB::connection('MAX')
                    ->table('CIEV_V_EstadoOP')
                    ->where('ORDNUM_14', '=', $forecast->OP)
                    ->orderBy('OPRSEQ_14', 'asc')
                    ->get();
            }

            return response()->json([
                'number' => $request->forecast,
                'forecasts' => $forecasts,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get_inventory
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_inventory(Request $request): JsonResponse
    {
        try {
            $inventory = DB::connection('MAX')
                ->table('CIEV_V_Inventario')
                ->where('pieza', '=', $request->reference)
                ->first();

            return response()->json($inventory, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * lots_detail
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function lots_detail(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_Inventario')
                ->where('pieza', '=', $request->reference)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * committed_amount
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function committed_amount(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_OVAbiertas')
                ->where('REFERENCIA', '=', $request->reference)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
