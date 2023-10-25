<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
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

class AccountReceivableController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $sellers = User::where('occupation', '=', 'vendedor')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Applications/Report/Sales/AccountReceivable', [
            'sellers' => $sellers
        ]);
    }

    /**
     * @param Request $request
     * @return Mpdf
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function download(Request $request)
    {
        $seller = User::where('vendor_code', '=', $request->seller)->first();

        $data = DB::connection('DMS')
            ->table('V_CIEV_CARTERA_EDADES_DNL')
            ->where('codigo_vendedor', '=', $request->seller)
            ->get();

        $data = $data->groupBy('nit');

        $pdf = $this->initPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.Reports.sales.account-receivable.header', compact('seller')));
        $pdf->SetHTMLFooter(View::make('pdfs.Reports.sales.account-receivable.footer'));
        $pdf->WriteHTML(View::make('pdfs.Reports.sales.account-receivable.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    protected function initPDF()
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => 'Letter-L',
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
            'margin_bottom' => 20,
            'margin_header' => 5,
            'margin_footer' => 6,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/cash_receipt/styles.css')), HTMLParserMode::HEADER_CSS);

        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("CI Estrada Velasquez & CIA SAS - Análisis de cuentas por cobrar detallado");
        $pdf->SetAuthor("CI Estrada Velasquez & CIA SAS");
        $pdf->SetWatermarkText("ANÁLISIS DE CUENTAS POR COBRAR DETALLADO");
        $pdf->showWatermarkText = true;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }
}
