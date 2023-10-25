<?php

namespace App\Http\Controllers\ThirdParties\CashRegisterReceipts;

use App\Http\Controllers\Controller;
use App\Models\CustomerMax;
use App\Models\HeaderCashReceipt;
use App\Models\LogCashReceipt;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class ReceiptController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.third-parties.cash-register-receipts', [
            'only' => [
                'index', 'update',
            ],
        ]);

        $this->middleware('permission:application.third-parties.cash-register-receipts.create', [
            'only' => [
                'index', 'create', 'cancel', 'send_wallet', 'search_customer', 'get_customer_receipts', 'store', 'edit',
            ],
        ]);
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        if (Auth::user()->hasRole('super-admin')) {
            $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                ->orderBy('consecutive', 'desc')
                ->get();
        } else {
            $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                ->where('created_id', '=', auth()->user()->id)
                ->orderBy('consecutive', 'desc')
                ->get();
        }

        return Inertia::render('Applications/ThirdParties/CashRegisterReceipts/Index', [
            'cash_receipt' => $cash_receipt,
        ]);
    }

    /**
     * search_customer
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function search_customer(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            $results = [];

            $queries = DB::connection('DMS')
                ->table('V_CIEV_Clientes')
                ->where('nombres', 'LIKE', '%'.$query.'%')
                ->orWhere('nit', 'LIKE', '%'.$query.'%')
                ->take(20)
                ->get();

            foreach ($queries as $q) {
                $results[] = [
                    'name' => trim($q->nombres),
                    'nit' => trim($q->nit),
                    'code' => trim($q->Codigo),
                ];
            }

            return response()->json($results, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get_customer_receipts
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_customer_receipts(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('DMS')
                ->table('V_CIEV_Saldofacturas')
                ->where('nit', '=', $request->get('customer_nit'))
                ->where('saldo', '>', 0)
                ->orderBy('numero', 'asc')
                ->get();

            $ObjectData = [];

            foreach ($data as $row) {
                $ObjectData[] = [
                    'invoice' => $row->numero,
                    'bruto' =>  round($row->Saldo, 2),
                    'bruto_it' => round($row->ValorTotal, 2),
                    'discount_foot' => round($row->descuento_pie, 2),
                    'discount' => floatval(0),
                    'iva' => round($row->iva, 2),
                    'retention' => floatval(0),
                    'original_retention' => round($row->retencion, 2),
                    'reteiva' => floatval(0),
                    'reteica' => floatval(0),
                    'other_deductions' => floatval(0),
                    'other_income' => floatval(0),
                    'merchandise_value' => round($row->valor_mercancia, 2),
                    'total' => floatval(0),
                    'trm' => round($row->TRM, 2),
                    'financial_expenses' => floatval(0),
                    'positive_balance' => floatval(0)
                ];
            }

            return response()->json($ObjectData, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * store
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'type' => 'required|string',
            'customer_code' => 'required|string',
            'payment_date' => 'required|date',
            'paid_value' => 'required',
            'account' => 'required|string',
            'payment_method' => 'required|string',
            'invoices' => 'required|array|min:1',
            'invoices.*' => 'required|array',
            'total_settled' => 'required',
        ])->validate();

        DB::beginTransaction();
        try {
            $cash_receipt = HeaderCashReceipt::create([
                'type' => $request->type,
                'customer_code' => $request->customer_code,
                'total_paid' => $request->paid_value,
                'comments' => $request->comments,
                'payment_date' => Carbon::parse($request->payment_date)->format('Y-m-d'),
                'payment_account' => $request->account,
                'payment_method' => $request->payment_method,
                'state' => '1',
                'created_by' => auth()->user()->id,
            ]);

            $cash_receipt->details()->createMany($request->invoices);

            $cash_receipt->log()->create([
                'description' => 'creo un recibo de caja',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json('cash receipt saved correctly', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * create
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Applications/ThirdParties/CashRegisterReceipts/Create');
    }

    /**
     * FunctionName
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function search_document(Request $request): JsonResponse
    {
        try {
            $query = DB::connection('DMS')
                ->table('V_CIEV_Saldofacturas')
                ->where('numero', '=', $request->invoice)
                ->first();

            return response()->json($query, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * consult_sales_type
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function consult_sales_type(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('invoice_master')
                ->where('INVCE_31', '=', $request->invoice)
                ->pluck('REASON_31')
                ->first();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * view
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function view(Request $request): JsonResponse
    {
        try {
            //$data = HeaderCashReceipt::with('details', 'customer', 'createdby', 'log.user')->find($request->id);

            $data = DB::table('V_CASH_RECEIPT_HEADERS')
                ->where('id', '=', $request->id)
                ->first();

            $data->details = DB::table('V_CASH_RECEIPT_DETAILS')
                ->where('cash_receipt_id', '=', $request->id)
                ->get();

            $data->log = LogCashReceipt::with('user')
                ->where('cash_receipt_id', '=', $request->id)
                ->get();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * cancel
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        try {
            $cash_receipt = HeaderCashReceipt::find($request->id);

            $cash_receipt->update([
                'state' => '0',
            ]);

            $cash_receipt->log()->create([
                'description' => 'recibo de caja anulado, Justificacion: '.$request->justify,
                'created_by' => Auth::id(),
            ]);

            if (Auth::user()->hasRole('super-admin')) {
                $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                    ->orderBy('consecutive', 'desc')
                    ->get();
            } else {
                $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                    ->where('created_id', '=', auth()->user()->id)
                    ->orderBy('consecutive', 'desc')
                    ->get();
            }

            return response()->json($cash_receipt, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @param $id
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'paid_value' => 'required',
            'account' => 'required|string',
            'payment_method' => 'required|string',
            'invoices' => 'required|array|min:1',
            'invoices.*' => 'required|array',
            'total_settled' => 'required',
        ])->validate();

        DB::beginTransaction();
        try {
            $cash_receipt = HeaderCashReceipt::find($id);

            $cash_receipt->update([
                'total_paid' => $request->paid_value,
                'comments' => $request->comments,
                'payment_date' => Carbon::parse($request->payment_date)->format('Y-m-d'),
                'payment_account' => $request->account,
                'payment_method' => $request->payment_method,
                'state' => '1',
            ]);

            $cash_receipt->details()->delete();
            $cash_receipt->details()->createMany($request->invoices);

            $cash_receipt->log()->create([
                'description' => 'actualizo el recibo de caja',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json('cash receipt saved correctly', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * send_wallet
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function send_wallet(Request $request): JsonResponse
    {
        try {
            $cash_receipt = HeaderCashReceipt::find($request->id);

            $cash_receipt->update([
                'state' => '2',
            ]);

            $cash_receipt->log()->create([
                'description' => 'recibo de caja enviado a cartera',
                'created_by' => Auth::id(),
            ]);

            if (Auth::user()->hasRole('super-admin')) {
                $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                    ->orderBy('consecutive', 'desc')
                    ->get();
            } else {
                $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
                    ->where('created_id', '=', auth()->user()->id)
                    ->orderBy('consecutive', 'desc')
                    ->get();
            }

            return response()->json($cash_receipt, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function edit($id): Response
    {
        $cash_receipt = DB::table('V_CASH_RECEIPT_HEADERS')
            ->where('id', '=', $id)
            ->first();

        $cash_receipt->details = DB::table('V_CASH_RECEIPT_DETAILS')
            ->where('cash_receipt_id', '=', $id)
            ->get();

        $data = DB::connection('DMS')
            ->table('V_CIEV_Saldofacturas')
            ->where('nit', '=', explode('-', $cash_receipt->customer_nit))
            ->where('saldo', '>', 0)
            ->orderBy('numero', 'asc')
            ->get();

        $ObjectData = [];

        foreach ($data as $row) {
            $ObjectData[] = [
                'invoice' => $row->numero,
                'bruto' =>  round($row->Saldo, 2),
                'bruto_it' => round($row->ValorTotal, 2),
                'discount_foot' => round($row->descuento_pie, 2),
                'discount' => floatval(0),
                'iva' => round($row->iva, 2),
                'retention' => floatval(0),
                'original_retention' => round($row->retencion, 2),
                'reteiva' => floatval(0),
                'reteica' => floatval(0),
                'other_deductions' => floatval(0),
                'other_income' => floatval(0),
                'merchandise_value' => round($row->valor_mercancia, 2),
                'total' => floatval(0),
                'trm' => round($row->TRM, 2),
                'financial_expenses' => floatval(0),
                'positive_balance' => floatval(0)
            ];
        }

        return Inertia::render('Applications/ThirdParties/CashRegisterReceipts/Edit', [
            'cash_receipt' => $cash_receipt,
            'invoices' => $ObjectData,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function report_cash_receipt(Request $request): JsonResponse
    {
        $date = explode(' - ', $request->date);

        $data = auth()->user()->hasRole('super-admin')
            ? HeaderCashReceipt::where('state', '=', '4')
                ->whereBetween('created_at', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->sum('total_paid')
            : HeaderCashReceipt::where('created_by', '=', Auth::id())
                ->where('state', '=', '4')
                ->whereBetween('created_at', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->sum('total_paid');

        return response()->json($data, 200);
    }

    /**
     * @return Response
     */
    public function account_status(){
        return Inertia::render('Applications/ThirdParties/CashRegisterReceipts/AccountStatus');
    }

    /**
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function account_status_pdf(Request $request)
    {
        $customer = CustomerMax::find($request->customer_code);
        $nit = explode('-', $customer->NIT);

        $data = DB::connection('DMS')
            ->table('V_CIEV_Saldofacturas')
            ->where('nit', '=',  $nit[0])
            ->where('saldo', '>', 0)
            ->orderBy('numero', 'asc')
            ->get()->map(function ($row) {
                return (object)[
                    'invoice' => $row->numero,
                    'date' => Carbon::parse($row->fecha)->format('Y-m-d'),
                    'expiration' => Carbon::parse($row->vencimiento)->format('Y-m-d'),
                    'total' => $row->ValorTotal,
                    'payments' =>  $row->valor_aplicado,
                    'balance' => $row->Saldo,
                    'moneda'=> $row->moneda,
                    'trm' => $row->TRM,
                    'expiration_days' => Carbon::now()->gte($row->vencimiento),
                    'documents' => DB::connection('DMS')
                        ->table('documentos_cruce')
                        ->where('numero_aplica', '=', $row->numero)
                        ->get()
                ];
            });

        $pdf = $this->initMPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.cash_receipt.header', compact('customer')));
        $pdf->SetHTMLFooter(View::make('pdfs.cash_receipt.footer'));
        $pdf->WriteHTML(View::make('pdfs.cash_receipt.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response|mixed
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function download_cash_receipts_export(Request $id){

        $data = DB::table('V_CASH_RECEIPT_HEADERS')
            ->where('id', '=', $id->id)
            ->first();

        $data->details = DB::table('V_CASH_RECEIPT_DETAILS')
            ->where('cash_receipt_id', '=', $id->id)
            ->get();

        $data->log = LogCashReceipt::with('user')
            ->where('cash_receipt_id', '=', $id->id)
            ->get();

        $pdf = $this->exportPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.cash_receipt_export.header', compact('data')));
        $pdf->SetHTMLFooter(View::make('pdfs.cash_receipt_export.footer'));
        $pdf->WriteHTML(View::make('pdfs.cash_receipt_export.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    private function initMPDF(){
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => 'Letter-P',
            'fontdata' => $fontData + [
                    'Roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ],
                ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 35,
            'margin_bottom' => 15,
            'margin_header' => 5,
            'margin_footer' => 6,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/cash_receipt/styles.css')), HTMLParserMode::HEADER_CSS);

        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("CI Estrada Velasquez & CIA SAS - Estado de cuenta");
        $pdf->SetAuthor("CI Estrada Velasquez & CIA SAS");
        $pdf->SetWatermarkText("Estado de cuenta");
        $pdf->showWatermarkText = true;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_trm(Request $request)
    {
        $query = DB::connection('DMS')
            ->table('monedas_factores')
            ->where('moneda', '=', 'US')
            ->where('fecha', '=', Carbon::parse($request->date)->format('Y-m-d 00:00:00'))
            ->first();

        return response()->json($query, 200);
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    private function exportPDF(){
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => 'Letter-P',
            'fontdata' => $fontData + [
                    'Roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ],
                ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 35,
            'margin_bottom' => 15,
            'margin_header' => 5,
            'margin_footer' => 6,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/cash_receipt_export/styles.css')), HTMLParserMode::HEADER_CSS);

        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("CI ESTRADA VELASQUEZ & CIA SAS ");
        $pdf->SetAuthor("CI Estrada Velasquez & CIA SAS");
        $pdf->showWatermarkText = false;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }

}
