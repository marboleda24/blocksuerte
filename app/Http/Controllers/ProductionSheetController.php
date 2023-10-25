<?php

namespace App\Http\Controllers;

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

class ProductionSheetController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/ProductionSheet');
    }

    /**
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function pdf(Request $request)
    {
        $pdf = $this->initPDF();

        for ($i = intval($request->start); $i <= intval($request->end); $i++) {

            $op = DB::connection('MAX')
                ->table('CIEV_V_OP_OV_V1')
                ->where('OP', '=', $i)
                ->first();

            $job_progress = DB::connection('MAX')
                ->table('Job_Progress')
                ->where('ORDNUM_14', '=', str_pad($i, 12, '0'))
                ->get();

            $material = DB::connection('MAX')
                ->table('Requirement_Detail')
                ->join('Part_Master', 'PRTNUM_11', '=', 'PRTNUM_01')
                ->where('ORDER_11', '=', str_pad($i, 12, '0'))
                ->where('COMCDE_01', '<>', 'QUIMICOS')
                ->first();

            $pdf->SetHTMLHeader(View::make('pdfs.ProductionSheet.header', compact('op', 'material')), 'O', true);
            $pdf->SetHTMLFooter(View::make('pdfs.ProductionSheet.footer'));
            $pdf->WriteHTML(View::make('pdfs.ProductionSheet.template', compact('job_progress', 'material')), HTMLParserMode::HTML_BODY);

            if ($i < intval($request->end)){
                $pdf->AddPage('', '', true);

            }
        }

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
            'margin_left' => 3,
            'margin_right' => 3,
            'margin_top' => 25,
            'margin_bottom' => 10,
            'margin_header' => 3,
            'margin_footer' => 4,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/ProductionSheet/styles.css')), HTMLParserMode::HEADER_CSS);

        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("CI ESTRADA VELASQUEZ & CIA SAS - Hoja de produccion");
        $pdf->SetAuthor("CI ESTRADA VELASQUEZ & CIA SAS");
        $pdf->showWatermarkText = true;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }
}
