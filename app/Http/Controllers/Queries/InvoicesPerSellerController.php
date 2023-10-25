<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InvoicesPerSellerController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:queries.invoices-per-seller');
    }

    /**
     * @return Response
     */
    public function index()
    {
        $sellers = User::where('occupation', '=', 'vendedor')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Applications/Queries/InvoicesPerSeller', [
            'sellers' => $sellers,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
                ->where('CODVENDEDOR', '=', $request->seller)
                ->whereIn('TIPODOC', ['CU', 'CR'])
                ->whereBetween('FECHA', [
                    Carbon::parse($request->start_date)->format('Y-m-d'),
                    Carbon::parse($request->end_date)->format('Y-m-d'),
                ])
                ->orderBy($request->orderBy === 'invoice' ? 'NUMERO' : 'RAZONSOCIAL')
                ->get();

            return response()->json($query, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $invoice
     * @return JsonResponse
     */
    public function show($invoice)
    {
        try {
            $header = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
                ->where('TIPODOC', '=', 'CU')
                ->where('NUMERO', '=', $invoice)
                ->first();

            $detail = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasDetalladas')
                ->where('Factura', '=', $invoice)
                ->get()->map(function ($row) use($invoice){
                    $row->notes = DB::connection('MAX')
                        ->table('CIEV_V_FE_NotasFacturas_Dian')
                        ->where('Factura', '=', $invoice)
                        ->where('Item', '=', $row->Item)
                        ->where('OV', '=', $row->OV)
                        ->whereNotNull('Nota')
                        ->pluck('Nota');

                    return $row;
                });

            return response()->json([
                'header' => $header,
                'detail' => $detail,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
