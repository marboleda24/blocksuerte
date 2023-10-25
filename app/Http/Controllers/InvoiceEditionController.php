<?php

namespace App\Http\Controllers;

use App\Models\Dian\ApiDocument;
use App\Models\InvoiceEditionLog;
use App\Models\MAXInvoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class InvoiceEditionController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/InvoiceEdition');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search_invoice(Request $request)
    {
        try {
            $invoice = MAXInvoice::with('details')
                ->where('NUMERO', '=', $request->invoice)
                ->first();

            if (!$invoice) {
                throw new Exception("No se encontro ningun documento", 402);
            }

            if ($invoice->state) {
                throw new Exception("Este documento ya ha sido enviado a la DIAN y no puede ser editado", 402);
            }

            return response()->json($invoice, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            $check_status = ApiDocument::where('state_document_id', '=', 1)
                ->where('number', '=', $request->NUMERO)
                ->count();

            if ($check_status > 0) {
                throw new Exception("Este documento ya ha sido enviado a la DIAN y no puede ser editado", 402);
            }

            DB::connection('MAX')
                ->table('Invoice_Master')
                ->where('ORDNUM_31', '=', $request->OV)
                ->where('INVCE_31', '=', str_pad($request->NUMERO, 8, '0', STR_PAD_LEFT))
                ->update([
                    'LNETOT_31' => number_format($request->BRUTO, 2, '.', ''),
                    'ORDDSC_31' => number_format($request->DESCUENTO, 2, '.', ''),
                    'TAX1_31' => number_format($request->taxable ? $request->IVA : 0, 2, '.', ''),
                    'TAXTOT_31' => number_format($request->BRUTO - $request->DESCUENTO, 2, '.', ''),
                    'TAXABL_31' => $request->taxable ? 'Y' : 'N',
                    'TAXCD1_31' => $request->taxable ? 'IVA-V19' : '',
                    'ModifiedBy' => "EVPIU-" . Auth::user()->username,
                ]);

            $retentions = $this->calculate_retention(number_format($request->BRUTO - $request->DESCUENTO, 2, '.', ''),
                number_format($request->taxable ? $request->IVA : 0, 2, '.', ''),
                $request->MOTIVO, $request->CLIENTE);

            if ($retentions->success) {
                DB::connection('MAX')
                    ->table('Invoice_Master_EXT')
                    ->where('INVCE_31', '=', str_pad($request->NUMERO, 8, '0', STR_PAD_LEFT))
                    ->update([
                        'RTEFTE' => $retentions->retefuente->tax,
                        'RETEIVA' => $retentions->reteiva->tax,
                    ]);
            } else {
                throw new Exception("Error procesando calculo de las retenciones", 500);
            }

            DB::connection('MAX')
                ->table('SO_Master')
                ->where('ORDNUM_27', '=', $request->OV)
                ->update([
                    'TAXCD1_27' => $request->taxable ? 'IVA-V19' : '',
                    'TAXABL_27' => $request->taxable ? 'Y' : 'N',
                    'ModifiedBy' => "EVPIU-" . Auth::user()->username,
                ]);

            foreach ($request->details as $detail) {
                $discount_percent = ($request->DESCUENTO * 100) / $request->BRUTO;
                $item_bruto = $detail['Precio'] * $detail['Cantidad'];
                $discount_value = ($discount_percent * $item_bruto) / 100;

                DB::connection('MAX')
                    ->table('Invoice_Detail')
                    ->where('ORDNUM_32', '=', $request->OV)
                    ->where('INVCE_32', '=', str_pad($request->NUMERO, 8, '0', STR_PAD_LEFT))
                    ->where('LINNUM_32', '=', substr($detail['Item'], 0, 2))
                    ->where('DELNUM_32', '=', substr($detail['Item'], 2, 2))
                    ->update([
                        'TAX1_32' => $request->taxable ? (($item_bruto - $discount_value) * 0.19) : 0,
                        'TAXCDE1_32' => $request->taxable ? 'IVA-V19' : '',
                        'TAXABL_32' => $request->taxable ? 'Y' : 'N',
                        'PRICE_32' => $detail['Precio'],
                        'ModifiedBy' => "EVPIU-" . Auth::user()->username,
                    ]);

                DB::connection('MAX')
                    ->table('SO_Detail')
                    ->where('ORDNUM_28', '=', $request->OV)
                    ->where('LINNUM_28', '=', substr($detail['Item'], 0, 2))
                    ->where('DELNUM_28', '=', substr($detail['Item'], 2, 2))
                    ->update([
                        'TAX1_28' => $request->taxable ? (($item_bruto - $discount_value) * 0.19) : 0,
                        'TAXCDE1_28' => $request->taxable ? 'IVA-V19' : '',
                        'TAXABL_28' => $request->taxable ? 'Y' : 'N',
                        'PRICE_28' => $detail['Precio'],
                        'FORCUR_28' => $detail['Precio'],
                        'ModifiedBy' => "EVPIU-" . Auth::user()->username,
                    ]);
            }

            InvoiceEditionLog::create([
                'invoice' =>  $request->NUMERO,
                'user_id' => Auth::id()
            ]);

            DB::connection('MAX')->commit();
            return response()->json("success", 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $subtotal
     * @param $tax
     * @param $reason
     * @param $customer
     * @return object
     */
    protected function calculate_retention($subtotal, $tax, $reason, $customer)
    {
        try {
            $type_customer = DB::connection('DMS')
                ->table('Terceros')
                ->where('codigo_alterno', '=', $customer)
                ->first();

            $retefuente = DB::connection('MAX')
                ->table('CIEV_ParametrosReteFuente')
                ->where('Ano', '=', Carbon::now()->format('Y'))
                ->where('Tipo', '=', $reason === '24' ? "SERVICIOS" : "VENTAS")
                ->whereRaw("$subtotal >= Base")
                ->first();

            $reteiva = DB::connection('MAX')
                ->table('CIEV_ParametrosReteFuente')
                ->where('Ano', '=', Carbon::now()->format('Y'))
                ->where('Tipo', '=', $reason === '24' ? "RI-SERVICIOS" : "RI-VENTAS")
                ->whereRaw("$subtotal >= Base")
                ->first();

            $result = (object)[
                "success" => true,
                "retefuente" => (object)[
                    "tasa" => $retefuente ? $retefuente->Tasa : 0,
                    "tax" => number_format($retefuente ? ($retefuente->Tasa * $subtotal) / 100 : 0, 2, '.', ''),
                    "name" => $retefuente ? $retefuente->Tipo : 'NA'
                ],
            ];

            if ($type_customer && $type_customer->gran_contribuyente){
                $result->reteiva = (object)[
                    "tasa" => $reteiva ? $reteiva->Tasa : 0,
                    "tax" => number_format($reteiva ? ($reteiva->Tasa * $tax) / 100 : 0, 2, '.', ''),
                    "name" => $reteiva ? $reteiva->Tipo : 'NA'
                ];
            }else {
                $result->reteiva = (object)[
                    "tasa" => 0,
                    "tax" => number_format(0, 2, '.', ''),
                    "name" => 'NA'
                ];
            }

            return $result;
        } catch (Exception $e) {
            return (object)[
                "success" => false,
                "msg" => $e->getMessage()
            ];
        }
    }


}
