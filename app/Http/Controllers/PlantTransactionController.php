<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PlantTransactionController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/PlantTransaction');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $query = DB::connection('MAX')
            ->table('CIEV_V_TransaccionesPlanta')
            ->where('OP', '=', str_pad($request->production_order, 12, '0', STR_PAD_RIGHT))
            ->orderBy('FECHA')
            ->orderBy('HORA')
            ->get();

        return response()->json($query, 200);
    }
}
