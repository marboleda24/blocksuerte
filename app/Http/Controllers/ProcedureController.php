<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProcedureController extends Controller
{
    public function upload_invoice_export_dms(): Response
    {
        return Inertia::render('Applications/Procedures/UploadExportInvoiceDMS');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_by_date(Request $request): JsonResponse
    {
        try {
            $date = explode(' - ', $request->date);

            $invoices = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
                ->where('MONEDA', '=', 'USD')
                ->whereBetween('FECHA', [
                    Carbon::parse($date[0])->format('Y-m-d'),
                    Carbon::parse($date[1])->format('Y-m-d'),
                ])
                ->get()
                ->map(function ($row) {
                    return [
                        'number' => $row->NUMERO,
                        'customer' => $row->RAZONSOCIAL,
                        'bruto' => $row->BRUTO_USD,
                        'fletes' => $row->FLETES_USD,
                        'seguros' => $row->SEGUROS_USD,
                        'dms' => DB::connection('DMS')->table('Documentos')->where('numero', '=', $row->NUMERO)->first(),
                    ];
                });

            return response()->json($invoices, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function upload_documents_dms(Request $request)
    {
        try {
            $result = [];

            foreach ($request->documents as $document) {
                $procedure = DB::connection('MAX')
                    ->update('SET NOCOUNT ON; EXEC CIEV_PA_Fact_GenerarTransferenciaFactExportacionesADMS @FAC ='.$document);

                $result[] = $procedure;
            }

            $date = explode(' - ', $request->date_range);

            $documents = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
                ->where('MONEDA', '=', 'USD')
                ->whereBetween('FECHA', [
                    Carbon::parse($date[0])->format('Y-m-d'),
                    Carbon::parse($date[1])->format('Y-m-d'),
                ])
                ->get()
                ->map(function ($row) {
                    return [
                        'number' => $row->NUMERO,
                        'customer' => $row->RAZONSOCIAL,
                        'bruto' => $row->BRUTO_USD,
                        'fletes' => $row->FLETES_USD,
                        'seguros' => $row->SEGUROS_USD,
                        'dms' => DB::connection('DMS')->table('Documentos')->where('numero', '=', $row->NUMERO)->first(),
                    ];
                });

            return response()->json([
                'documents' => $documents,
                'result' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
