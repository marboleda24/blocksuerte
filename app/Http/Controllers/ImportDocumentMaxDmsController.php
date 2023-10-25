<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ImportDocumentMaxDmsController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function index($entity)
    {
        $switch = (bool) SystemSetting::find($entity === 'CIEV' ? 1 : 2)
            ->automatic_import_DMS_MAX;

        return Inertia::render('Applications/ImportDocumentMaxDms', [
            'switch' => $switch,
            'entity' => $entity
        ]);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function activate_desactivate(Request $request, $entity)
    {
        SystemSetting::find($entity === 'CIEV' ? 1 : 2)
            ->update([
                'automatic_import_DMS_MAX' => (bool) $request->state,
            ]);

        return response()->json((bool) $request->state, 200);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function search(Request $request, $entity)
    {
        try {
            $date = explode(' - ', $request->date);

            $query = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
                ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
                ->whereBetween('FECHA', [
                    Carbon::parse($date[0])->format('Y-m-d'),
                    Carbon::parse($date[1])->format('Y-m-d'),
                ])
                ->where('TIPODOC', '=', 'CU')
                ->orderBy('NUMERO')
                ->get()->map(function ($row) use($entity){
                    $exist_document = DB::connection($entity === 'CIEV' ? 'DMS' : 'GOJA')
                        ->table('documentos')
                        ->where('sw', '=', '1')
                        ->where('tipo', '=', $entity === 'CIEV' ? 'FAC' : 'FRA')
                        ->where('numero', '=', $row->NUMERO)
                        ->count();

                    $row->DMS = $exist_document > 0;

                    return $row;
                });

            return response()->json($query, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     * @throws Throwable
     */
    public function import_documents(Request $request, $entity)
    {
        try {
            $result = [];

            foreach ($request->documents as $document) {
                $checkReason = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
                    ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
                    ->where('NUMERO', '=', $document)
                    ->pluck('MOTIVO')
                    ->first();

                $result[] = $this->import_document($entity, $document, $checkReason === '39' ? '39' : null);
            }

            return response()->json([
                'result' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $entity
     * @param $document
     * @param $reason
     * @return array
     * @throws Throwable
     */
    protected function import_document($entity, $document, $reason = null)
    {
        $connection = $entity === 'CIEV' ? 'DMS' : 'GOJA';
        $document_name = $entity === 'CIEV' ? 'FAC' : 'FRA';

        DB::connection($connection)->beginTransaction();
        try {
            $exist_document = DB::connection($connection)
                ->table('documentos')
                ->where('sw', '=', '1')
                ->where('tipo', '=', $document_name)
                ->where('numero', '=', $document)
                ->count();

            if ($exist_document > 0) {
                DB::connection($connection)->rollBack();

                return [
                    'document' => $document,
                    'code' => 402,
                    'msg' => 'Este documento ya ha sido importando a DMS',
                ];
            } else {
                $header = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
                    ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
                    ->where('NUMERO', '=', $document)
                    ->first();

                if (! $header) {
                    return [
                        'document' => $document,
                        'code' => 402,
                        'msg' => 'Documento no valido o inexistente en MAX',
                    ];
                }

                $total_paid = (($header->SUBTOTAL) + $header->IVA + $header->FLETES + $header->SEGUROS) - ($header->RTEFTE + $header->RTEIVA);

                $model = null;
                $concept = null;
                $account_cxc = null;
                $account_sell = null;

                if ($entity === 'CIEV'){
                    if ($reason === '39'){
                        $model = $header->CODIVA === 'VATV' ? 'VPN' : 'VRC';
                        $concept = 1;
                        $account_cxc = '13050505';
                        $account_sell = '41209505';
                    }else {
                        switch ($header->TIPOCLIENTE) {
                            case 'ZF':
                                $model = 'VZF';
                                $concept = 6;
                                $account_cxc = '13050505';
                                if ($header->IVA > 0) {
                                    $account_sell = '41209515';
                                } else {
                                    $account_sell = '41209521';
                                }

                                break;
                            case 'CI':
                                if ($header->IVA > 0) {
                                    $model = 'VCI';
                                    $concept = 2;
                                    $account_cxc = '13050505';
                                    $account_sell = '41209515';
                                } else {
                                    $model = 'VCIS';
                                    $concept = 3;
                                    $account_cxc = '13050505';
                                    $account_sell = '41209510';
                                }
                                break;
                            case 'PN':
                                $model = 'VPN';
                                $concept = 1;
                                $account_cxc = '13050505';
                                $account_sell = '41209505';
                                break;
                            case 'RC':
                                $model = $header->CODIVA === 'VATV' ? 'VPN' : 'VRC';
                                $concept = 1;
                                $account_cxc = '13050505';
                                $account_sell = '41209505';
                                break;
                            case 'EX':
                                $model = 'VEX';
                                $concept = 1;
                                $account_cxc = '13051010';
                                $account_sell = '41209520';
                                break;
                        }
                    }
                }else {
                    switch ($header->TIPOCLIENTE) {
                        case 'PN':
                            $model = 'VPN';
                            $concept = 1;
                            $account_cxc = '13050505';
                            $account_sell = '41205005';
                            break;
                        case 'RC':
                            $model = $header->CODIVA === 'VATV' ? 'VPN' : 'VRC';
                            $concept = 1;
                            $account_cxc = '13050505';
                            $account_sell = '41205005';
                            break;
                        case 'EX':
                            $model = 'VEX';
                            $concept = 1;
                            $account_cxc = '13051010';
                            $account_sell = '41209520';
                            break;
                    }
                }

                DB::connection($connection)
                    ->table('documentos')
                    ->insert([
                        'sw' => '1',
                        'tipo' => $document_name,
                        'numero' => $document,
                        'nit' => $header->IDENTIFICACION,
                        'fecha' => $header->FECHA, /* crear input para fecha */
                        'condicion' => $header->PLAZO === '00' ? '10' : $header->PLAZO,
                        'vencimiento' => $header->VENCIMIENTO, /*mismo valor de fecha*/
                        'valor_total' => round($total_paid), /* valor pagado en RC*/
                        'iva' => round($header->IVA),
                        'retencion' => round($header->RTEFTE), /* retencion RC*/
                        'retencion_causada' => 0,
                        'fletes' => round($header->FLETES + $header->SEGUROS),
                        'retencion_iva' => round($header->RTEIVA),
                        'retencion_ica' => 0,
                        'descuento_pie' => round($header->DESCUENTO),
                        'iva_fletes' => 0,
                        'costo' => 0,
                        'vendedor' => $header->CODVENDEDOR, /* vendedor asociado a la factura*/
                        'valor_aplicado' => 0, /* valor pagado en RC*/
                        'anulado' => 0,
                        'modelo' => $model,
                        'documento' => $header->OV, /* dejar en blanco*/
                        'notas' => 'Importado desde EVPIU el '.Carbon::now()->format('Y-m-d'), /* comentarios del RC*/
                        'usuario' => Auth::user()->username,
                        'pc' => gethostname(),
                        'fecha_hora' => Carbon::now(),
                        'moneda' =>  $reason === '39' ? 'US' : ($header->MONEDA === 'USD' ? 'US' : null),
                        'tasa' => $reason === '39' ? number_format($header->TASA, 2, '.', '') : ($header->MONEDA === 'USD' ? number_format($header->TASA, 2, '.', '') : null),
                        'retencion2' => 0,
                        'retencion3' => 0,
                        'bodega' => 1,
                        'impoconsumo' => 0,
                        'descuento2' => 0,
                        'duracion' => 1,
                        'concepto' => $concept,
                        'impuesto_deporte' => 0,
                        'centro_doc' => $reason === '39' ? 0 : ($header->MONEDA === 'USD' ? 0 : $header->MOTIVO),
                        'valor_mercancia' => round($header->BRUTO),
                    ]);

                $seq = 2;

                DB::connection($connection)
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $document_name,
                        'numero' => $document,
                        'seq' => 1,
                        'cuenta' => $account_cxc,
                        'centro' => 0,
                        'nit' => $header->IDENTIFICACION,
                        'fec' => $header->FECHA,
                        'valor' => round($total_paid),
                        'base' => 0,
                        'documento' => $header->OV,
                    ]);

                DB::connection($connection)
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $document_name,
                        'numero' => $document,
                        'seq' => 2,
                        'cuenta' => $account_sell,
                        'centro' => 0,
                        'nit' => $header->IDENTIFICACION,
                        'fec' => $header->FECHA,
                        'valor' => -round($header->SUBTOTAL),
                        'base' => 0,
                        'documento' => $header->OV,
                    ]);

                if ($header->IVA > 0) {
                    $seq++;
                    DB::connection($connection)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $document_name,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '24080507',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => -round($header->IVA),
                            'base' => round($header->SUBTOTAL),
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->RTEFTE > 0) {
                    $seq++;
                    DB::connection($connection)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $document_name,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '13551501',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => round($header->RTEFTE),
                            'base' => round($header->SUBTOTAL),
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->RTEIVA > 0) {
                    $seq++;
                    DB::connection($connection)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $document_name,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '13551705',
                            'centro' => 0,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => round($header->RTEIVA),
                            'base' => round($header->SUBTOTAL),
                            'documento' => $header->OV,
                        ]);
                }

                if ($header->FLETES > 0 || $header->SEGUROS > 0) {
                    $seq++;
                    DB::connection($connection)
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $document_name,
                            'numero' => $document,
                            'seq' => $seq,
                            'cuenta' => '42505005',
                            'centro' => 13,
                            'nit' => $header->IDENTIFICACION,
                            'fec' => $header->FECHA,
                            'valor' => -round(($header->FLETES + $header->SEGUROS)),
                            'base' => 0,
                            'documento' => $header->OV,
                        ]);
                }

                DB::connection($connection)->commit();

                return [
                    'document' => $document,
                    'code' => 200,
                    'msg' => 'Importación exitosa',
                ];
            }
        } catch (Exception $e) {
            DB::connection($connection)->rollBack();

            return [
                'document' => $document,
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
            ];
        }
    }
}
