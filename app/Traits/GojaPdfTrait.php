<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

trait GojaPdfTrait
{
    /**
     * @param $document
     * @param $year
     * @param $month
     * @param $period
     * @param $period_str
     * @return Mpdf
     *
     * @throws MpdfException
     */
    protected function createPDF($document, $year, $month, $period, $period_str): Mpdf
    {
        $employee = DB::connection('GOJA')
            ->table('v_Personal')
            ->where('nit', '=', $document)
            ->where('estado', '=', 'A')
            ->first();

        $items = DB::connection('GOJA')
            ->table('v_Liquidaciones')
            ->where('IDENTIFICACION', '=', $document)
            ->where('AÑO', '=', $year)
            ->where('MES', '=', $month)
            ->where('PERIODO', '=', $period)
            ->get();

        $contract = DB::connection('GOJA')
            ->table('V_PG_NomContratos')
            ->where('nit', '=', $document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->first();

        $loans = DB::connection('GOJA')
            ->table('v_PrestamosPersonal')
            ->where('IDENTIFICACION', '=', $document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->where('SALDO', '>', 0)
            ->get();

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.proof_payment_goja.header', compact('document', 'employee', 'items', 'contract', 'loans', 'period_str')));
        $pdf->SetHTMLFooter(View::make('pdfs.proof_payment_goja.footer', compact('document', 'employee', 'items', 'contract', 'loans')));
        $pdf->WriteHTML(View::make('pdfs.proof_payment_goja.template', compact('document', 'employee', 'items', 'contract', 'loans')), HTMLParserMode::HTML_BODY);

        return $pdf;
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
            'margin_top' => 20,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/proof_payment_goja/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }
}
