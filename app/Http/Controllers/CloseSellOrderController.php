<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CloseSellOrderController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/Queries/CloseSellOrder');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $date = explode(' - ', $request->dateRange);
            $query = DB::connection('MAX')
                ->table('CIEV_V_OVCerradas')
                ->where(function ($query) use ($request, $date) {
                    if ($request->type === 'customer') {
                        $query->where('COD_CLIENTE', '=', $request->customer)
                            ->where('FECHA_OV', '>', Carbon::now()->subYears(3)->format('Y-m-d'));
                    } else if ($request->type === 'dateRange') {
                        $query->whereBetween('FECHA_OV', [Carbon::parse($date[0])->format('Y-d-m'), Carbon::parse($date[1])->format('Y-d-m')]);
                    }
                })->orderBy('OV', 'desc')
                ->get();

            $collection = $query->groupBy('RAZON_SOCIAL');

            return response()->json($collection, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
