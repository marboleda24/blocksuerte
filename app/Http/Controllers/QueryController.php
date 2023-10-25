<?php

namespace App\Http\Controllers;

use App\Models\EncoderMeasurementDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QueryController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/Queries/ProductOC');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $q = DB::connection('MAX')
            ->table('CIEV_V_Facturas_CodProdCliente_OC')
            ->where('OC', 'LIKE', "%$request->oc%")
            ->where('CODPRODCLIENTE', 'LIKE', "%$request->cpc%")
            ->get();

        return response()->json($q, 200);
    }

    /**
     * @return Response
     */
    public function codes_list()
    {
        $codes = DB::table('codes_list')
            ->get()
            ->map(function ($row) {
                $measurements = EncoderMeasurementDetail::with('unit', 'characteristic')
                    ->where('measurement_id', '=', $row->measurement_id)
                    ->get();

                $row->max_status = DB::connection('MAX')
                        ->table('Part_Master')
                        ->where('PRTNUM_01', '=', $row->code)->count() > 0;
                $row->measurement = denominationCreator($measurements);

                return $row;
            });

        return Inertia::render('Applications/Queries/CodesList', [
            'codes' => $codes,
        ]);
    }
}
