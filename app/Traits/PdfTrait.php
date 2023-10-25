<?php

namespace App\Traits;

use App\Models\JobLetterRequest;
use App\Models\Signature;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Luecano\NumeroALetras\NumeroALetras;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

trait PdfTrait
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
        $employee = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $document)
            ->where('estado', '=', 'A')
            ->first();

        $items = DB::connection('DMS')
            ->table('V_CIEV_Liquidaciones')
            ->where('IDENTIFICACION', '=', $document)
            ->where('AÑO', '=', $year)
            ->where('MES', '=', $month)
            ->where('PERIODO', '=', $period)
            ->get();

        $contract = DB::connection('DMS')
            ->table('V_CIEV_NomContratos')
            ->where('nit', '=', $document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->first();

        $loans = DB::connection('DMS')
            ->table('V_CIEV_PrestamosPersonal')
            ->where('IDENTIFICACION', '=', $document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->where('ESTADO_EMPLEADO', '=', 'ACTIVO')
            ->where('SALDO', '>', 0)
            ->get();

        $pdf = $this->initMPdf();

        $pdf->SetHTMLHeader(View::make('pdfs.proof_payment.header', compact('employee', 'period_str', 'contract')));
        $pdf->SetHTMLFooter(View::make('pdfs.proof_payment.footer'));
        $pdf->WriteHTML(View::make('pdfs.proof_payment.template', compact('document', 'employee', 'items', 'contract', 'loans')), HTMLParserMode::HTML_BODY);

        return $pdf;
    }

    /**
     * @param $employee_document
     * @param $approve_user_document
     * @param $approve_user_id
     * @param $request_id
     * @return \Barryvdh\DomPDF\PDF
     */
    protected function working_letter($employee_document, $approve_user_document, $approve_user_id, $request_id): \Barryvdh\DomPDF\PDF
    {
        $contract = DB::connection('DMS')
            ->table('V_CIEV_NomContratos')
            ->where('nit', '=', $employee_document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->first();

        $employee_info = DB::connection('DMS')
            ->table('V_CIEV_EmpleadosCentroCostos')
            ->where('nit', '=', $employee_document)
            ->where('estado', '=', 'A')
            ->first();

        $approve_user_info = DB::connection('DMS')
            ->table('V_CIEV_EmpleadosCentroCostos')
            ->where('nit', '=', $approve_user_document)
            ->where('estado', '=', 'A')
            ->first();

        $commissions = DB::connection('DMS')
            ->table('V_CIEV_Liquidaciones')
            ->where('CONCEPTO', '=', 80)
            ->where('IDENTIFICACION', '=', $employee_document)
            ->whereDate('FIN', '>', Carbon::now()->subMonths(6)->format('Y-m-d 00:00:00'))
            ->sum('VALOR');

        $commissions = ($commissions / 6);

        $salary = new NumeroALetras();
        $salary = $salary->toMoney($contract->basico_mes + $commissions, 2, 'PESOS', 'CENTAVOS');

        $imgBase64 = Signature::find($approve_user_id);
        $imgBase64 = base64_encode(file_get_contents(storage_path($imgBase64->path)));
        $signature_image = 'data:image/png;base64, '.$imgBase64;

        $request_info = JobLetterRequest::find($request_id);

        $pdf = PDF::loadView('working_letter',
            compact('contract', 'employee_info', 'salary',
                'approve_user_info', 'signature_image', 'request_info', 'commissions')
        );

        $pdf->setWarnings(false);
        $pdf->setPaper('letter', 'portrait');

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
            'margin_top' => 35,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/proof_payment/styles.css')), HTMLParserMode::HEADER_CSS);
        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("Comprobante de nomina");
        $pdf->SetAuthor("CI ESTRADA VELASQUEZ & CIA SAS");
        $pdf->SetWatermarkText("Comprobante de nomina");
        $pdf->showWatermarkText = true;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }

    /**
     * @param $employee_document
     * @param $approve_user_document
     * @param $approve_user_id
     * @return \Barryvdh\DomPDF\PDF
     */
    protected function retired_employee_pdf($employee_document, $approve_user_document, $approve_user_id): \Barryvdh\DomPDF\PDF
    {
        $contract = DB::connection('DMS')
            ->table('V_CIEV_NomContratos')
            ->where('nit', '=', $employee_document)
            ->where('ESTADO', '=', 'RETIRADO')
            ->orderBy('fecha_ingreso', 'desc')
            ->first();

        $employee_info = DB::connection('DMS')
            ->table('V_CIEV_EmpleadosCentroCostos')
            ->where('nit', '=', $employee_document)
            ->where('estado', '=', 'R')
            ->first();

        $approve_user_info = DB::connection('DMS')
            ->table('V_CIEV_EmpleadosCentroCostos')
            ->where('nit', '=', $approve_user_document)
            ->where('estado', '=', 'A')
            ->first();

        $imgBase64 = Signature::find($approve_user_id);
        $imgBase64 = base64_encode(file_get_contents(storage_path($imgBase64->path)));
        $signature_image = 'data:image/png;base64, '.$imgBase64;

        $pdf = PDF::loadView('retired_employee',
            compact('contract', 'approve_user_info', 'signature_image', 'employee_info')
        );

        $pdf->setWarnings(false);
        $pdf->setPaper('letter', 'portrait');

        return $pdf;
    }
}
