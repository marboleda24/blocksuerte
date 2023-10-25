<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

trait PolishedDeliveryControlTrait
{
    /**
     * @param $start_date
     * @param $end_date
     * @return Mpdf
     *
     * @throws MpdfException
     */
    protected function generatePDF($start_date, $end_date): Mpdf
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_ControlEntregas')
            ->where('CT', '=', 'PULIR')
            ->whereBetween('FECHA', [$start_date, $end_date])
            ->orderBy('TIPO_PRODUCTO')->orderBy('FECHA')
            ->get();

        $data = $data->groupBy('TIPO_PRODUCTO');

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.PolishedDeliveryControl.header', compact('start_date', 'end_date')));
        $pdf->SetHTMLFooter(View::make('pdfs.PolishedDeliveryControl.footer'));
        $pdf->WriteHTML(View::make('pdfs.PolishedDeliveryControl.template', compact('data')), HTMLParserMode::HTML_BODY);

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
            'orientation' => 'L',
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
            'margin_top' => 15,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/PolishedDeliveryControl/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }
}
