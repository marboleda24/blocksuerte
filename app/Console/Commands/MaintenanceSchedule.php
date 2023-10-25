<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class MaintenanceSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:maintenance-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @return void
     * @throws MpdfException
     */
    public function handle(): void
    {
        try {
            $date2 = $this->format_date(now());

            $date[0] = Carbon::now()->format('Y-m-1');
            $date[1] = Carbon::now()->format('Y-m-t');

            $query = DB::table('V_MAINTENANCE_SCHEDULE')
                ->whereBetween('next', $date)
                ->orderBy('week')
                ->get()
                ->groupby('week');

            $pdf = $this->preventivePDF();
            $pdf->SetHTMLHeader(View::make('pdfs.maintenance.preventive.header', compact('date')));
            $pdf->SetHTMLFooter(View::make('pdfs.maintenance.preventive.footer'));
            $pdf->WriteHTML(View::make('pdfs.maintenance.preventive.template', compact('query')), HTMLParserMode::HTML_BODY);

            if (count($query) === 0) {
                Mail::to(['dcorrea@estradavelasquez.com', 'dmtabares@estradavelasquez.com'])
                    ->send(new SystemNotificationMail('CRONOGRAMA DE MANTENIMIENTOS', "Cronograma de  mantenimientos $date2", "EVPIU le informa que usted no tiene mantenimientos para el  mes  $date2", 'notify'));

            } else {
                Mail::to(['dcorrea@estradavelasquez.com', 'dmtabares@estradavelasquez.com'])
                    ->send(new SystemNotificationMail("CRONOGRAMA DE MANTENIMIENTOS", "Cronograma de  mantenimientos $date2", " Adjunto encontrara el cronograma de mantenimientos preventivos  del mes $date2", 'notify', [], $pdf));
            }

        } catch (Exception $e) {
            Mail::to(['dcorrea@estradavelasquez.com', 'dmtabares@estradavelasquez.com'])
                ->send(new SystemNotificationMail('Error', 'Error en el cronograma de mantenimientos preventivo', "EVPIU le informa que hubo un error en el cronograma de este mes, error: {$e->getMessage()}", 'notify'));
        }
    }

    /**
     * @param $date
     * @return string
     */
    function format_date($date): string
    {
        $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $month = $months[($date->format('n')) - 1];
        return ' de ' . $month;
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    public function preventivePDF(): Mpdf
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
            'margin_top' => 33,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/maintenance/preventive/styles.css')), HTMLParserMode::HEADER_CSS);
        $pdf->SetProtection(array('print'));
        $pdf->SetTitle("CI ESTRADA VELASQUEZ & CIA SAS ");
        $pdf->SetAuthor("CI Estrada Velasquez & CIA SAS");
        $pdf->showWatermarkText = false;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }
}
