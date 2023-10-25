<?php

namespace App\Http\Controllers\ElectronicBilling\National;

use App\Http\Controllers\Controller;
use App\Models\Dian\Invoices;
use App\Models\Goja\Invoice;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class InvoiceController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function index($entity): Response
    {
        return Inertia::render('Applications/ElectronicBilling/National/Invoices', [
            'entity' => $entity
        ]);
    }

    /**
     * search_by_date
     *
     * @param mixed $request
     * @param $entity
     * @return JsonResponse
     */
    public function search_by_date(Request $request, $entity): JsonResponse
    {
        $date = explode(' - ', $request->date);

        if ($entity === 'CIEV') {
            $data = Invoices::whereBetween('fecha', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->where('tipodoc', '=', 'CU')
                ->where('tipocliente', '!=', 'EX')
                ->where('moneda', '=', 'COP')
                ->orderBy('numero', 'asc')
                ->get();

        } else {
            $data = Invoice::whereBetween('fecha', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->where('tipodoc', '=', 'CU')
                ->where('tipocliente', '!=', 'EX')
                ->where('moneda', '=', 'COP')
                ->orderBy('numero', 'asc')
                ->get();

        }
        return response()->json($data, 200);
    }

    /**
     * @param $entity
     * @param $document
     * @return void
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function remission($entity, $document)
    {
        $invoice = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? 'CIEV_V_FE_FacturasTotalizadas_Dian' : 'PG_V_FE_FacturasTotalizadas_Dian')
            ->where('NUMERO', '=', $document)
            ->first();

        $details = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? "CIEV_V_FacturasDetalladas" : 'PG_V_FacturasDetalladas')
            ->where('Factura', '=', $document)
            ->get();

        $item_notes = DB::connection($entity === 'CIEV' ? 'MAX' : 'MAXPG')
            ->table($entity === 'CIEV' ? "CIEV_V_NotasFacturas" : 'PG_V_NotasFacturas')
            ->where('Factura', '=', $document)
            ->where('OV', '=', $invoice->OV)
            ->get();

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.invoice.header', compact('invoice', 'entity')));
        $pdf->SetHTMLFooter(View::make('pdfs.invoice.footer'));
        $pdf->WriteHTML(View::make('pdfs.invoice.template', compact('invoice', 'details', 'item_notes')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOC(Request $request)
    {
        try {
            DB::connection('MAXPG')
                ->table('Invoice_Master')
                ->where('INVCE_31', '=', str_pad($request->invoice, 8, '0', STR_PAD_LEFT))
                ->update([
                    'CUSTPO_31' => $request->oc
                ]);

            return response()->json('success', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @throws MpdfException
     */
    protected function initMPdf(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
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
            'margin_top' => 30,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 1,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/invoice/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }
}
