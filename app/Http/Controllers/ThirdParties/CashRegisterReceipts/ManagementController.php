<?php

namespace App\Http\Controllers\ThirdParties\CashRegisterReceipts;

use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use App\Models\Advance;
use App\Models\HeaderCashReceipt;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Luecano\NumeroALetras\NumeroALetras;
use Throwable;

class ManagementController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.third-parties.management-advances-receipt');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/ThirdParties/ManagementAdvancesReceipt', [
            'advances' => Advance::with('createdby', 'customer')
                ->where('state', '=', '2')
                ->get(),
            'cash_receipts' => HeaderCashReceipt::with('customer', 'createdby', 'approvedby')
                ->where('state', '=', '2')
                ->get(),
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function approve_receipt(Request $request): JsonResponse
    {
        DB::connection('DMS')->beginTransaction();
        DB::beginTransaction();
        try {
            $receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                ->where('id', '=', $request->id)
                ->first();

            $receipt->details = DB::table('V_CASH_RECEIPT_DETAILS')
                ->where('cash_receipt_id', '=', $request->id)
                ->get();

            $rc_type = $receipt->type === 'national' ? 'RCCO' : 'RCEX';

            $last_receipt = DB::connection('DMS')
                ->table('documentos')
                ->where('tipo', '=', $rc_type)
                ->max('numero');

            $bank = DB::connection('DMS')
                ->table('bancos')
                ->where('cuenta', '=', $receipt->payment_account)
                ->pluck('banco')
                ->first();

            $formatter = new NumeroALetras;

            $value_letters = $formatter->toMoney($receipt->type === 'national' ? $receipt->total_paid : intval($receipt->total_paid * $receipt->trm), 0, 'PESOS', 'CENTAVOS');
            $sequence_flag = 2;
            $services_retencion = 0;
            $sales_retention = 0;

            if ($receipt->type === 'export'){
                DB::connection('DMS')
                    ->table('documentos')
                    ->insert([
                        'sw' => '5',
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'nit' => $receipt->customer_nit,
                        'fecha' => $receipt->payment_date, /* crear input para fecha */
                        'condicion' => null,
                        'vencimiento' => $receipt->payment_date, /*mismo valor de fecha*/
                        'valor_total' => intval($receipt->total_paid * $receipt->trm), /* valor pagado en RC*/
                        'iva' => null,
                        'retencion' => 0, /* retencion RC*/
                        'retencion_causada' => null,
                        'retencion_iva' => 0,
                        'retencion_ica' => 0,
                        'descuento_pie' => intval($receipt->details->sum('discount') * $receipt->trm),
                        'fletes' => null, /* otros ingresos */
                        'iva_fletes' => gmp_neg(intval($receipt->ajust)),
                        'costo' => null,
                        'vendedor' => $receipt->seller, /* vendedor asociado a la factura*/
                        'valor_aplicado' => intval($receipt->total_paid * $receipt->trm), /* valor pagado en RC*/
                        'anulado' => '0',
                        'modelo' => '*',
                        'documento' => $request->consecutive, /* dejar en blanco*/
                        'notas' => $receipt->comments, /* comentarios del RC*/
                        'usuario' => Auth::user()->username,
                        'pc' => gethostname(),
                        'fecha_hora' => Carbon::now(),
                        'retencion2' => '0',
                        'retencion3' => '0',
                        'bodega' => '1',
                        'duracion' => '5',
                        'concepto' => '1',
                        'centro_doc' => '0',
                        'tasa' => $receipt->trm,
                        'moneda' => 'US'
                    ]);
            }else {
                DB::connection('DMS')
                    ->table('documentos')
                    ->insert([
                        'sw' => '5',
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'nit' => $receipt->customer_nit,
                        'fecha' => $receipt->payment_date, /* crear input para fecha */
                        'condicion' => null,
                        'vencimiento' => $receipt->payment_date, /*mismo valor de fecha*/
                        'valor_total' => $receipt->total_paid, /* valor pagado en RC*/
                        'iva' => null,
                        'retencion' => $receipt->details->sum('retention'), /* retencion RC*/
                        'retencion_causada' => null,
                        'retencion_iva' => $receipt->details->sum('reteiva'),
                        'retencion_ica' => $receipt->details->sum('reteica'),
                        'descuento_pie' => $receipt->details->sum('discount'),
                        'fletes' => null, /* otros ingresos */
                        'iva_fletes' => gmp_neg(intval($receipt->details->sum('other_income'))),
                        'costo' => null,
                        'vendedor' => $receipt->seller, /* vendedor asociado a la factura*/
                        'valor_aplicado' => $receipt->total_paid, /* valor pagado en RC*/
                        'anulado' => '0',
                        'modelo' => '1',
                        'documento' => $request->consecutive, /* dejar en blanco*/
                        'notas' => $receipt->comments, /* comentarios del RC*/
                        'usuario' => Auth::user()->username,
                        'pc' => gethostname(),
                        'fecha_hora' => Carbon::now(),
                        'retencion2' => '0',
                        'retencion3' => '0',
                        'bodega' => '1',
                        'duracion' => '5',
                        'concepto' => '1',
                        'centro_doc' => '0',
                    ]);
            }

            if ($receipt->type === 'export'){
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => 1,
                        'cuenta' => '13051010',
                        'centro' => '0',
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => gmp_neg(intval($receipt->total_paid_exclude)),
                        'documento' => '1',
                    ]);
            }else {
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => 1,
                        'cuenta' => '13050505',
                        'centro' => '0',
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => gmp_neg(intval($receipt->details->sum('bruto'))),
                        'documento' => '1',
                    ]);
            }

            if ($receipt->type === 'export'){
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => 2,
                        'cuenta' => $receipt->payment_account,
                        'centro' => 0,
                        'nit' => 0,
                        'fec' => $receipt->payment_date,
                        'valor' => intval($receipt->total_paid * $receipt->trm),
                        'documento' => '1',
                        'explicacion' => $receipt->comments,
                        'concilio' => null,
                    ]);
            }else {
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => 2,
                        'cuenta' => $receipt->payment_account,
                        'centro' => 0,
                        'nit' => 0,
                        'fec' => $receipt->payment_date,
                        'valor' => $receipt->total_paid,
                        'documento' => '1',
                        'explicacion' => $receipt->comments,
                        'concilio' => null,
                    ]);
            }

            if ($receipt->details->sum('discount') > 0) {
                $sequence_flag += 1;

                if ($receipt->type === 'export'){
                    DB::connection('DMS')
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'seq' => $sequence_flag,
                            'cuenta' => 41752060,
                            'centro' => 0,
                            'nit' => $receipt->customer_nit,
                            'fec' => $receipt->payment_date,
                            'valor' => intval($receipt->details->sum('discount') * $receipt->trm),
                            'documento' => '1',
                        ]);
                }else {
                    DB::connection('DMS')
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'seq' => $sequence_flag,
                            'cuenta' => 41752060,
                            'centro' => 0,
                            'nit' => $receipt->customer_nit,
                            'fec' => $receipt->payment_date,
                            'valor' => $receipt->details->sum('discount'),
                            'documento' => '1',
                        ]);
                }
            }

            if ($receipt->details->sum('other_income') > 0) {
                $sequence_flag += 1;

                if ($receipt->type === 'export'){
                    DB::connection('DMS')
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'seq' => $sequence_flag,
                            'cuenta' => 53959505,
                            'centro' => 0,
                            'nit' => $receipt->customer_nit,
                            'fec' => $receipt->payment_date,
                            'valor' => gmp_neg(intval($receipt->details->sum('other_income') * $receipt->trm)),
                            'documento' => '1',
                        ]);
                }else {
                    DB::connection('DMS')
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'seq' => $sequence_flag,
                            'cuenta' => 42104010,
                            'centro' => 0,
                            'nit' => $receipt->customer_nit,
                            'fec' => $receipt->payment_date,
                            'valor' => gmp_neg(intval($receipt->details->sum('other_income'))),
                            'documento' => '1',
                        ]);
                }
            }

            if ($receipt->details->sum('other_deductions') > 0) {
                $sequence_flag += 1;

                if ($receipt->type === 'export'){
                    DB::connection('DMS')
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'seq' => $sequence_flag,
                            'cuenta' => 42104010,
                            'centro' => 0,
                            'nit' => $receipt->customer_nit,
                            'fec' => $receipt->payment_date,
                            'valor' => intval($receipt->details->sum('other_deductions') * $receipt->trm),
                            'documento' => '1',
                        ]);
                }else {
                    DB::connection('DMS')
                        ->table('movimiento')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'seq' => $sequence_flag,
                            'cuenta' => 53959505,
                            'centro' => 0,
                            'nit' => $receipt->customer_nit,
                            'fec' => $receipt->payment_date,
                            'valor' => $receipt->details->sum('other_deductions'),
                            'documento' => '1',
                        ]);
                }
            }

            if ($receipt->details->sum('reteica') > 0 && $receipt->type === 'national') {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => 13551005,
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => $receipt->details->sum('reteica'),
                        'documento' => '1',
                    ]);
            }

            if ($receipt->details->sum('reteiva') > 0 && $receipt->type === 'national') {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => 13551705,
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => $receipt->details->sum('reteiva'),
                        'documento' => '1',
                    ]);
            }

            if ($receipt->details->sum('financial_expenses') > 0 && $receipt->type === 'export'){
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => 53051510,
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => intval($receipt->details->sum('financial_expenses') * $receipt->trm),
                        'documento' => '1',
                    ]);
            }

            if ($receipt->change_difference && $receipt->type === 'export'){
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => $receipt->change_difference > 0 ? '42102005' : '53052505',
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => ($receipt->change_difference >= 0) ? gmp_neg(intval($receipt->change_difference)) : abs(intval($receipt->change_difference)),
                        'documento' => '1',
                    ]);
            }

            $total_difference = 0;

            foreach ($receipt->details as $row) {

                if ($receipt->details->sum('retention') > 0 && $receipt->type === 'national') {
                    $query = DB::connection('MAX')
                        ->table('invoice_master')
                        ->where('INVCE_31', '=', '00'.$row->invoice)
                        ->pluck('REASON_31')
                        ->first();

                    if ($query == '24') {
                        $services_retencion = $services_retencion + $row->retention;
                    } else {
                        $sales_retention = $sales_retention + $row->retention;
                    }
                }

                $document_type = DB::connection('DMS')
                    ->table('documentos')
                    ->where('numero', '=', $row->invoice)
                    ->whereIn('tipo', ['FP1', 'FP2', 'FP3', 'FP4', 'FP5', 'FP6', 'FAC'])
                    ->where('nit', '=', $receipt->customer_nit)
                    ->pluck('tipo')
                    ->first();

                $document = DB::connection('DMS')
                    ->table('documentos')
                    ->where('tipo', '=', $document_type)
                    ->where('numero', '=', $row->invoice)
                    ->get(['fecha', 'valor_aplicado'])
                    ->first();


                if ($receipt->type === 'export'){
                    DB::connection('DMS')
                        ->table('documentos_cruce')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'sw' => '1',
                            'tipo_aplica' => $document_type,
                            'numero_aplica' => $row->invoice,
                            'numero_cuota' => '0',
                            'valor' => intval($row->bruto * $row->trm),
                            'descuento' => intval($row->discount * $receipt->trm),
                            'retencion' => 0,
                            'retencion_iva' => 0,
                            'retencion_ica' => 0,
                            'fecha' => $document->fecha,
                            'fecha_cruce' => Carbon::now(),
                            'ajuste' => intval(($row->other_income + $row->change_difference) - ($row->other_deductions + $row->financial_expenses))
                        ]);

                    $balance  = DB::connection('DMS')
                        ->table('V_CIEV_Saldofacturas')
                        ->where('numero', '=', $row->invoice)
                        ->first();

                    $diff = 0;
                    if ($balance->ValorTotal > 100000 && $balance->ValorTotal - ($document->valor_aplicado + ($row->bruto * $receipt->trm)) > 0 && $balance->ValorTotal - ($document->valor_aplicado + ($row->bruto * $receipt->trm)) <= 2000){
                        $diff = $balance->ValorTotal - ($document->valor_aplicado + ($row->bruto * $receipt->trm));
                        $total_difference += $diff;
                    }


                    DB::connection('DMS')
                        ->table('documentos')
                        ->where('tipo', '=', $document_type)
                        ->where('numero', '=', $row->invoice)
                        ->update([
                            'valor_aplicado' => $document->valor_aplicado + ($row->bruto * $receipt->trm) + $diff,
                        ]);

                }else {
                    DB::connection('DMS')
                        ->table('documentos_cruce')
                        ->insert([
                            'tipo' => $rc_type,
                            'numero' => $last_receipt + 1,
                            'sw' => '1',
                            'tipo_aplica' => $document_type,
                            'numero_aplica' => $row->invoice,
                            'numero_cuota' => '0',
                            'valor' => $row->bruto - ($row->discount + $row->retention + $row->reteiva + $row->reteica),
                            'descuento' => $row->discount,
                            'retencion' => $row->retention,
                            'retencion_iva' => $row->reteiva,
                            'retencion_ica' => $row->reteica,
                            'fecha' => $document->fecha,
                            'fecha_cruce' => Carbon::now(),
                        ]);

                    $balance  = DB::connection('DMS')
                        ->table('V_CIEV_Saldofacturas')
                        ->where('numero', '=', $row->invoice)
                        ->first();

                    $diff = 0;
                    if ($balance->ValorTotal > 100000 && $balance->ValorTotal - ($document->valor_aplicado + $row->bruto) > 0 && $balance->ValorTotal - ($document->valor_aplicado + $row->bruto) <= 2000){
                        $diff = $balance->ValorTotal - ($document->valor_aplicado + $row->bruto);
                        $total_difference += $diff;
                    }

                    DB::connection('DMS')
                        ->table('documentos')
                        ->where('tipo', '=', $document_type)
                        ->where('numero', '=', $row->invoice)
                        ->update([
                            'valor_aplicado' => $document->valor_aplicado + $row->bruto + $diff,
                        ]);
                }
            }

            if ($total_difference > 0){
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => 42950505,
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => gmp_neg(intval($total_difference)),
                        'documento' => '1',
                    ]);
            }

            if ($services_retencion > 0 && $receipt->type === 'national') {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => 13551520,
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => $services_retencion,
                        'documento' => '1',
                    ]);
            }

            if ($sales_retention > 0 && $receipt->type === 'national') {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_receipt + 1,
                        'seq' => $sequence_flag,
                        'cuenta' => 13551505,
                        'centro' => 0,
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => $sales_retention,
                        'documento' => '1',
                    ]);
            }

            DB::connection('DMS')
                ->table('documentos_che')
                ->insert([
                    'sw' => '5',
                    'tipo' => $rc_type,
                    'numero' => $last_receipt + 1,
                    'banco' => $bank, //id banco
                    'documento' => '1',
                    'forma_pago' => $receipt->payment_method, // vacio
                    'fecha' => $receipt->payment_date,
                    'valor' => $receipt->type === 'national' ? $receipt->total_paid : intval($receipt->total_paid * $receipt->trm),
                    'consignar_en' => $bank,
                    'devuelto' => null,
                    'tipo_consignacion' => null,
                    'numero_consignacion' => null,
                    'fecha_devolucion' => null,
                    'cuenta_banco' => null,
                    'iva_tarjeta' => null,
                    'notas' => null,
                    'fecha_forma' => null,
                    'tipo_devuelto' => null,
                    'numero_devuelto' => null,
                ]);

            HeaderCashReceipt::find($request->id)
                ->update([
                    'state' => '4',
                    'dms_cash_receipt' => $last_receipt + 1,
                    'approved_by' => auth()->user()->id,
                ]);

            DB::connection('DMS')
                ->table('documentos_monto')
                ->insert([
                    'tipo' => $rc_type,
                    'numero' => $last_receipt + 1,
                    'monto' => $value_letters,
                ]);


            if ($receipt->positive_balance && $receipt->type === 'export'){
                $last_advance = DB::connection('DMS')
                    ->table('documentos')
                    ->where('tipo', '=', $rc_type)
                    ->max('numero');

                $associate_vendor = DB::connection('DMS')
                    ->table('terceros')
                    ->where('nit', '=', $receipt->customer_nit)
                    ->pluck('vendedor')
                    ->first();

                $formatter = new NumeroALetras;
                $value_letters = $formatter->toMoney(intval($receipt->positive_balance * $receipt->trm), 0, 'PESOS', 'CENTAVOS');

                DB::connection('DMS')
                    ->table('documentos')
                    ->insert([
                        'sw' => '5',
                        'tipo' => $rc_type,
                        'numero' => $last_advance + 1,
                        'nit' => $receipt->customer_nit,
                        'fecha' => $receipt->payment_date, /* crear input para fecha */
                        'vencimiento' => $receipt->payment_date, /*mismo valor de fecha*/
                        'valor_total' => intval($receipt->positive_balance * $receipt->trm), /* valor pagado en RC*/
                        'retencion' => 0, /* retencion RC*/
                        'retencion_iva' => 0,
                        'retencion_ica' => 0,
                        'descuento_pie' => 0,
                        'iva_fletes' => 0,
                        'vendedor' => intval($associate_vendor), /* vendedor asociado a la factura*/
                        'valor_aplicado' => 0, /* valor pagado en RC*/
                        'anulado' => 0,
                        'modelo' => '*',
                        'documento' => $request->consecutive, /* dejar en blanco*/
                        'notas' => "SALDO A FAVOR POR RC ". $last_receipt + 1, /* comentarios del RC*/
                        'usuario' => Auth::user()->username,
                        'pc' => gethostname(),
                        'fecha_hora' => Carbon::now(),
                        'retencion2' => '0',
                        'retencion3' => '0',
                        'bodega' => '1',
                        'duracion' => '5',
                        'concepto' => '1',
                        'centro_doc' => '0',
                    ]);

                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_advance + 1,
                        'seq' => 1,
                        'cuenta' =>  '13051010',
                        'centro' => '0',
                        'nit' => $receipt->customer_nit,
                        'fec' => $receipt->payment_date,
                        'valor' => -abs(intval($receipt->positive_balance * $receipt->trm)),
                        'documento' => '1',
                    ]);

                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_advance + 1,
                        'seq' => 2,
                        'cuenta' => $receipt->payment_account,
                        'centro' => 0,
                        'nit' => 0,
                        'fec' => $receipt->payment_date,
                        'valor' => intval($receipt->positive_balance * $receipt->trm),
                        'documento' => '1',
                        'explicacion' => "SALDO A FAVOR POR RC ". $last_receipt + 1,
                        'concilio' => null,
                    ]);

                DB::connection('DMS')
                    ->table('documentos_che')
                    ->insert([
                        'sw' => '5',
                        'tipo' => $rc_type,
                        'numero' => $last_advance + 1,
                        'banco' => $bank, //id banco
                        'documento' => '1',
                        'forma_pago' => '1', // vacio
                        'fecha' => $receipt->payment_date,
                        'valor' => intval($receipt->positive_balance * $receipt->trm),
                        'consignar_en' => $bank,
                    ]);

                DB::connection('DMS')
                    ->table('documentos_monto')
                    ->insert([
                        'tipo' => $rc_type,
                        'numero' => $last_advance + 1,
                        'monto' => $value_letters,
                    ]);


                HeaderCashReceipt::find($request->id)->log()->create([
                    'description' => "Genero un anticipo por saldo a favor en el recibo de caja",
                    'created_by' => Auth::id(),
                ]);
            }

            HeaderCashReceipt::find($request->id)->log()->create([
                'description' => 'Aprobó el recibo de caja',
                'created_by' => Auth::id(),
            ]);

            DB::connection('DMS')->commit();
            DB::commit();

            $cash_receipt = HeaderCashReceipt::with('createdby', 'customer')
                ->where('state', '=', '2')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'cash_receipts' => $cash_receipt,
                'dms_number' => $last_receipt + 1,
            ], 200);
        } catch (Exception $e) {
            DB::connection('DMS')->rollBack();
            DB::rollBack();

            return response()->json("{$e->getMessage()} – {$e->getLine()}", 500);
        }
    }

    /**
     * refuse_receipt
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function refuse_receipt(Request $request): JsonResponse
    {
        try {
            $receipt = HeaderCashReceipt::find($request->id);

            $receipt->update([
                'state' => '3',
            ]);

            $receipt->log()->create([
                'description' => 'Rechazo el recibo de caja, Justificación: '.$request->justify,
                'created_by' => Auth::id(),
            ]);

            $data = HeaderCashReceipt::with('createdby', 'customer')
                ->where('state', '=', '2')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * approve_advance
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function approve_advance(Request $request): JsonResponse
    {
        DB::connection('DMS')->beginTransaction();
        DB::beginTransaction();
        try {
            $advance = Advance::with('customer')
                ->find($request->id);

            $last_advance = DB::connection('DMS')
                ->table('documentos')
                ->where('tipo', '=', 'RCCO')
                ->max('numero');

            $associate_vendor = DB::connection('DMS')
                ->table('terceros')
                ->where('nit', '=', explode('-', $advance->customer->NIT)[0])
                ->pluck('vendedor')
                ->first();

            $client_concept = DB::connection('DMS')
                ->table('terceros')
                ->where('nit', '=', explode('-', $advance->customer->NIT)[0])
                ->pluck('concepto_4')
                ->first();

            $bank = DB::connection('DMS')
                ->table('bancos')
                ->where('cuenta', '=', $advance->bank_account)
                ->pluck('banco')
                ->first();

            $formatter = new NumeroALetras;
            $value_letters = $formatter->toMoney($advance->total_paid, 0, 'PESOS', 'CENTAVOS');

            DB::connection('DMS')
                ->table('documentos')
                ->insert([
                    'sw' => '5',
                    'tipo' => 'RCCO',
                    'numero' => $last_advance + 1,
                    'nit' => explode('-', $advance->customer->NIT)[0],
                    'fecha' => $advance->payment_date, /* crear input para fecha */
                    'vencimiento' => $advance->payment_date, /*mismo valor de fecha*/
                    'valor_total' => $advance->total_paid, /* valor pagado en RC*/
                    'retencion' => 0, /* retencion RC*/
                    'retencion_iva' => 0,
                    'retencion_ica' => 0,
                    'descuento_pie' => 0,
                    'iva_fletes' => 0,
                    'vendedor' => intval($associate_vendor), /* vendedor asociado a la factura*/
                    'valor_aplicado' => 0, /* valor pagado en RC*/
                    'anulado' => 0,
                    'modelo' => 1,
                    'documento' => $request->consecutive, /* dejar en blanco*/
                    'notas' => $advance->details, /* comentarios del RC*/
                    'usuario' => Auth::user()->username,
                    'pc' => gethostname(),
                    'fecha_hora' => Carbon::now(),
                    'retencion2' => '0',
                    'retencion3' => '0',
                    'bodega' => '1',
                    'duracion' => '5',
                    'concepto' => '1',
                    'centro_doc' => '0',
                ]);

            DB::connection('DMS')
                ->table('movimiento')
                ->insert([
                    'tipo' => 'RCCO',
                    'numero' => $last_advance + 1,
                    'seq' => 1,
                    'cuenta' =>  '13050505',
                    'centro' => '0',
                    'nit' => explode('-', $advance->customer->NIT)[0],
                    'fec' => $advance->payment_date,
                    'valor' => -abs($advance->total_paid),
                    'documento' => '1',
                ]);

            DB::connection('DMS')
                ->table('movimiento')
                ->insert([
                    'tipo' => 'RCCO',
                    'numero' => $last_advance + 1,
                    'seq' => 2,
                    'cuenta' => $advance->bank_account,
                    'centro' => 0,
                    'nit' => 0,
                    'fec' => $advance->payment_date,
                    'valor' => $advance->total_paid,
                    'documento' => '1',
                    'explicacion' => $advance->details,
                    'concilio' => null,
                ]);

            DB::connection('DMS')
                ->table('documentos_che')
                ->insert([
                    'sw' => '5',
                    'tipo' => 'RCCO',
                    'numero' => $last_advance + 1,
                    'banco' => $bank, //id banco
                    'documento' => '1',
                    'forma_pago' => '1', // vacio
                    'fecha' => $advance->payment_date,
                    'valor' => $advance->total_paid,
                    'consignar_en' => $bank,
                ]);

            $advance = Advance::find($request->id);
            $advance->update([
                'state' => '4',
                'dms_cash_receipt' => $last_advance + 1,
                'approved_by' => auth()->user()->id,
            ]);

            $advance->log()->create([
                'user_id' => Auth::id(),
                'description' => 'Anticipo Aprobado',
            ]);

            DB::connection('DMS')
                ->table('documentos_monto')
                ->insert([
                    'tipo' => 'RCCO',
                    'numero' => $last_advance + 1,
                    'monto' => $value_letters,
                ]);

            DB::connection('DMS')->commit();
            DB::commit();

            $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                ->where('state', '=', '2')
                ->orderBy('id', 'desc')
                ->get();

            return response()
                ->json([
                    'advances' => $advances,
                    'dms_number' => $last_advance + 1,
                ], 200);
        } catch (Exception $e) {
            DB::connection('DMS')->rollBack();
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * refuse_receipt
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function refuse_advance(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $advance = Advance::find($request->id);
            $advance->update([
                'state' => '3',
            ]);

            $advance->log()->create([
                'user_id' => Auth::id(),
                'description' => "Anticipo rechazado, Justificación: {$request->justify}",
            ]);

            if (isset($advance->createdby->email)) {
                Mail::to($advance->createdby->email)
                    ->send(new SystemNotificationMail('Anticipo rechazado', 'Anticipo rechazado', "EVPIU le informa que el anticipo {$advance->consecutive} ha sido rechazado por cartera, justificación: $request->justify"));
            }

            DB::commit();
            $advances = Advance::with('customer', 'createdby', 'approvedby', 'log')
                ->where('state', '=', '2')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($advances, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
