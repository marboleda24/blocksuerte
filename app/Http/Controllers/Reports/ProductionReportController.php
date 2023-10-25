<?php

namespace App\Http\Controllers\Reports;

use App\Exports\DeliveryControlExport;
use App\Exports\Report\MonitoringInjectionExport;
use App\Exports\Report\PendientesAutomaticasExport;
use App\Exports\Report\ProductionExport;
use App\Http\Controllers\Controller;
use App\Models\MultipleReport;
use App\Models\PendientesProduccion;
use App\Traits\PolishedDeliveryControlTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductionReportController extends Controller
{
    use PolishedDeliveryControlTrait;

    /**
     * @return Response
     */
    public function index(): Response
    {
        $other_reports = [
            (object)[
                "report" => "electro",
                "title" => "Electro",
            ],
            (object)[
                "report" => "deformadora-alambre",
                "title" => "Deformadora alambre",
            ],
            (object)[
                "report" => "troquelados-ojaletes",
                "title" => "Troquelados ojaletes",
            ],
            (object)[
                "report" => "encabezados",
                "title" => "Encabezados",
            ],
            (object)[
                "report" => "grabo-laser",
                "title" => "Grabo laser",
            ],
            (object)[
                "report" => "piedra",
                "title" => "Piedra",
            ],
            (object)[
                "report" => "pinturas",
                "title" => "Pinturas",
            ],
            (object)[
                "report" => "pintura-uv",
                "title" => "Pintura UV",
            ],
            (object)[
                "report" => "troquelados-engargolar",
                "title" => "troquelados engargolar",
            ],
            (object)[
                "report" => "troquelados-broches",
                "title" => "Troquelados broches",
            ],
            (object)[
                "report" => "troquelados-remaches",
                "title" => "Troquelados remaches",
            ],
            (object)[
                "report" => "troquelado-botones",
                "title" => "Troquelado botones",
            ],
            (object)[
                "report" => "inspeccion-empaque",
                "title" => "Inspeccion empaque",
            ],
        ];

        return Inertia::render('Applications/Report/Index', [
            'other_reports' => $other_reports,
        ]);
    }

    /**
     * @return Response
     */
    public function galvano1(): Response
    {
        $rows = PendientesProduccion::where('CT', '=', 'PLANT')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        return Inertia::render('Applications/Report/Production/Galvano1', [
            'rows' => $rows,
        ]);
    }

    /**
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function galvano1_pdf($sortBy)
    {
        $data = PendientesProduccion::where('CT', '=', 'PLANT')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        if ($sortBy === 'days'){
            $data = $data->sortByDesc($sortBy);
        }else {
            $data = $data->sortBy($sortBy);
        }

        $title = "INFORME GALVANO 1";

        $pdf = $this->initMPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.Reports.galvano1.header', compact('title')));
        $pdf->SetHTMLFooter(View::make('pdfs.Reports.galvano1.footer'));
        $pdf->WriteHTML(View::make('pdfs.Reports.galvano1.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param string $title
     * @return Mpdf
     * @throws MpdfException
     */
    private function initMPDF(string $title = ''): Mpdf
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
            'margin_bottom' => 14,
            'margin_header' => 5,
            'margin_footer' => 3,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/Reports/galvano1/styles.css')), HTMLParserMode::HEADER_CSS);

        $pdf->SetProtection(array('print', 'copy'));
        $pdf->SetTitle("CI ESTRADA VELASQUEZ & CIA SAS - $title");
        $pdf->SetAuthor("CI ESTRADA VELASQUEZ & CIA SAS");
        $pdf->SetWatermarkText($title);
        $pdf->showWatermarkText = true;
        $pdf->watermark_font = 'DejaVuSansCondensed';
        $pdf->watermarkTextAlpha = 0.1;

        return $pdf;
    }

    /**
     * @return Response
     */
    public function galvano2()
    {
        $rows = PendientesProduccion::where('CT', '=', 'GALV2')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        return Inertia::render('Applications/Report/Production/Galvano2', [
            'rows' => $rows,
        ]);
    }

    /**
     * @param string $sortBy
     * @return \Illuminate\Http\Response|mixed
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function galvano2_pdf(string $sortBy = 'days')
    {
        $data = PendientesProduccion::where('CT', '=', 'GALV2')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        if ($sortBy === 'days'){
            $data = $data->sortByDesc($sortBy);
        }else {
            $data = $data->sortBy($sortBy);
        }

        $title = "INFORME GALVANO 2";

        $pdf = $this->initMPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.Reports.galvano1.header', compact('title')));
        $pdf->SetHTMLFooter(View::make('pdfs.Reports.galvano1.footer'));
        $pdf->WriteHTML(View::make('pdfs.Reports.galvano1.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Response
     */
    public function estatica()
    {
        $rows = PendientesProduccion::where('CT', '=', 'STAT')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        return Inertia::render('Applications/Report/Production/Estatica', [
            'rows' => $rows,
        ]);
    }

    /**
     * @param string $sortBy
     * @return \Illuminate\Http\Response|mixed
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function estatica_pdf(string $sortBy = 'days'){
        $data = PendientesProduccion::where('CT', '=', 'STAT')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        if ($sortBy === 'days'){
            $data = $data->sortByDesc($sortBy);
        }else {
            $data = $data->sortBy($sortBy);
        }

        $title = "INFORME ESTATICA";

        $pdf = $this->initMPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.Reports.galvano1.header', compact('title')));
        $pdf->SetHTMLFooter(View::make('pdfs.Reports.galvano1.footer'));
        $pdf->WriteHTML(View::make('pdfs.Reports.galvano1.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Response
     */
    public function cnc()
    {
        $rows = PendientesProduccion::where('CT', '=', 'CNC')
            ->where('CANT_PENDIENTE', '>', 0)
            ->get();

        return Inertia::render('Applications/Report/Production/CNC', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return Response
     */
    public function zamac()
    {
        $rows = PendientesProduccion::where('CT', '=', 'ZAMAC')
            ->where('CANT_PENDIENTE', '>', 0)
            ->get();

        return Inertia::render('Applications/Report/Production/Zamac', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return Response
     */
    public function laser()
    {
        $rows = PendientesProduccion::where('CT', '=', 'LASER')
            ->where('CANT_PENDIENTE', '>', 0)
            ->get();

        return Inertia::render('Applications/Report/Production/Laser', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return Response
     */
    public function inspeccion_empaque(): Response
    {
        $rows = PendientesProduccion::where('CT', '=', 'ALMPT')
            ->where('CANT_PENDIENTE', '>', 0)
            ->get();

        return Inertia::render('Applications/Report/Production/InspeccionEmpaque', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return Response
     */
    public function uv(): Response
    {
        $rows = PendientesProduccion::where('CT', '=', 'UV')
            ->where('CANT_PENDIENTE', '>', 0)
            ->get();

        return Inertia::render('Applications/Report/Production/UV', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return Response
     */
    public function pulido(): Response
    {
        $rows = PendientesProduccion::where('CT', '=', 'PULIR')
            ->where('CANT_PENDIENTE', '>', 0)
            ->get();

        return Inertia::render('Applications/Report/Production/Pulido', [
            'rows' => $rows,
        ]);
    }

    /**
     * @param string $sortBy
     * @return \Illuminate\Http\Response|mixed
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function pulido_pdf(string $sortBy = 'days'){
        $data = PendientesProduccion::where('CT', '=', 'PULIR')
            ->where('CANT_PENDIENTE', '>', 0)
            ->orderBy('DIAS_OV', 'desc')
            ->get();

        if ($sortBy === 'days'){
            $data = $data->sortByDesc($sortBy);
        }else {
            $data = $data->sortBy($sortBy);
        }

        $title = "INFORME PULIDO";

        $pdf = $this->initMPDF();
        $pdf->SetHTMLHeader(View::make('pdfs.Reports.galvano1.header', compact('title')));
        $pdf->SetHTMLFooter(View::make('pdfs.Reports.galvano1.footer'));
        $pdf->WriteHTML(View::make('pdfs.Reports.galvano1.template', compact('data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Response
     */
    public function control_entregas_pulido()
    {
        return Inertia::render('Applications/Report/Production/ControlEntregasPulido');
    }

    /**
     * @return Response
     */
    public function pendientes_automaticas()
    {
        $rows = DB::connection('MAX')
            ->table('CIEV_V_PendientesAutomaticas')
            ->get();

        return Inertia::render('Applications/Report/Production/PendientesAutomaticas', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return Response
     */
    public function control_entregas_inyeccion()
    {
        return Inertia::render('Applications/Report/Production/ControlEntregasInyeccion');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_control_entregas_inyeccion(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);

            $data = DB::connection('MAX')
                ->table('CIEV_V_ControlEntregas')
                ->where('CT', '=', 'ZAMAC')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return Mpdf|string
     */
    public function control_entregas_inyeccion_pdf(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);
            $title = 'CONTROL ENTREGAS INYECCION';

            $data = DB::connection('MAX')
                ->table('CIEV_V_ControlEntregas')
                ->where('CT', '=', 'ZAMAC')
                ->select('OP', 'REFERENCIA', 'COD_PROD', 'PRODUCTO', 'CANT', 'ARTE')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->orderBy('PRODUCTO')
                ->get();

            $pdf = $this->initMPdf();
            $pdf->SetHTMLHeader(View::make('pdfs.Reports.header', compact('date', 'title')));
            $pdf->SetHTMLFooter(View::make('pdfs.Reports.footer'));
            $pdf->WriteHTML(View::make('pdfs.Reports.control_entregas_inyeccion', compact('data')), HTMLParserMode::HTML_BODY);

            return response()->make($pdf->Output(), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return Response
     */
    public function control_entregas_troquelado()
    {
        return Inertia::render('Applications/Report/Production/ControlEntregasTroquelado');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_control_entregas_troquelado(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);

            $data = DB::connection('MAX')
                ->table('CIEV_V_ControlEntregas')
                ->whereIn('TIPO_PRODUCTO', ['OJALETES', 'PR1', 'P1'])
                ->where('CT', '=', 'TROQL')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return Mpdf|string
     */
    public function control_entregas_troquelado_pdf(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);
            $title = 'CONTROL ENTREGAS TROQUELADO';
            $data = DB::connection('MAX')
                ->table('CIEV_V_ControlEntregas')
                ->select('OP', 'REFERENCIA', 'COD_PROD', 'PRODUCTO', 'CANT', 'ARTE')
                ->whereIn('TIPO_PRODUCTO', ['OJALETES', 'PR1', 'P1'])
                ->where('CT', '=', 'TROQL')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->get();

            $pdf = $this->initMPdf();
            $pdf->SetHTMLHeader(View::make('pdfs.Reports.header', compact('date', 'title')));
            $pdf->SetHTMLFooter(View::make('pdfs.Reports.footer'));
            $pdf->WriteHTML(View::make('pdfs.Reports.control_entregas_troquelado', compact('data')), HTMLParserMode::HTML_BODY);

            return response()->make($pdf->Output(), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return Response
     */
    public function control_entregas_automaticas()
    {
        return Inertia::render('Applications/Report/Production/ControlEntregasAutomaticas');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_control_entregas_automaticas(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);

            $data = DB::connection('MAX')
                ->table('CIEV_V_ControlEntregas')
                ->whereIn('TIPO_PRODUCTO', ['AUTOMATICA'])
                ->where('CT', '=', 'EAUT')
                ->where('RESPONSABLE', 'LIKE', 'A-%')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return Mpdf|string
     */
    public function control_entregas_automaticas_pdf(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);

            $title = 'CONTROL ENTREGAS AUTOMATICAS';

            $data = DB::connection('MAX')
                ->table('CIEV_V_ControlEntregas')
                ->whereIn('TIPO_PRODUCTO', ['AUTOMATICA'])
                ->where('CT', '=', 'EAUT')
                ->where('RESPONSABLE', 'LIKE', 'A-%')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->get();

            $pdf = $this->initMPdf();
            $pdf->SetHTMLHeader(View::make('pdfs.Reports.header', compact('date', 'title')));
            $pdf->SetHTMLFooter(View::make('pdfs.Reports.footer'));
            $pdf->WriteHTML(View::make('pdfs.Reports.control_entregas_automaticas', compact('data')), HTMLParserMode::HTML_BODY);

            return response()->make($pdf->Output(), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return BinaryFileResponse
     */
    public function download_report_pendientes_automaticas()
    {
        return Excel::download(new PendientesAutomaticasExport(), 'file.xlsx');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed|string
     */
    public function generate_polished_delivery_control_pdf(Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);

            $pdf = $this->generatePDF(Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d'));

            return response()->make($pdf->Output(), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return BinaryFileResponse
     */
    public function download_report(Request $request)
    {
        return Excel::download(new ProductionExport($request->plant), 'file.xlsx');
    }

    /**
     * @param $report
     * @param $type
     * @param string|null $sales
     * @return \Illuminate\Http\Response|Response|mixed
     *
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function multi_report($report, $type, string $sales = null)
    {
        $rpt = MultipleReport::where('code', '=', $report)->first();

        if ($sales) {
            $data = DB::connection('MAX')
                ->query()
                ->fromSub(function ($query) use ($rpt) {
                    $query->from($rpt->table)
                        ->select(DB::raw("*, ROW_NUMBER() OVER (PARTITION BY ARTE_OV ORDER BY FECHA_LIBERACION desc ) AS RowNumber"));
                }, 'a')
                ->where('RowNumber', '=', 1)
                ->get();
        } else {
            $data = DB::connection('MAX')
                ->table($rpt->table)
                ->get();
        }


        if ($type === 'report') {
            $pdf = $this->initMPdf();
            $pdf->SetHTMLHeader(View::make('pdfs.Reports.MultiReport.header', compact('rpt')));
            $pdf->SetHTMLFooter(View::make('pdfs.Reports.footer'));
            $pdf->WriteHTML(View::make('pdfs.Reports.MultiReport.template', compact('data')), HTMLParserMode::HTML_BODY);
            $pdf->SetTitle("Pendientes {$rpt->title} | EVPIU");

            $date = Carbon::now()->format('Y_m_d');

            return response()->make($pdf->Output("Pendientes_{$rpt->title}_$date", 'I'), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return Inertia::render('Applications/Report/Production/MultiReport', [
            'data' => $data,
            'report' => $rpt,
        ]);
    }

    /**
     * @return Response
     */
    public function ensamble_externo_report(): Response
    {
        return Inertia::render('Applications/Report/Production/EnsambleExterno');
    }

    /**
     * @return \Illuminate\Http\Response|Response|mixed
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function ensamble_externo_pdf(Request $request): mixed
    {
        $date = explode(' - ', $request->date_range);

        $data = DB::connection('MAX')
            ->table('CIEV_V_ControlEntregas')
            ->whereIn('CT', ['EEXT', 'AFUER'])
            ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
            ->orderBy('OP')
            ->get();

        $title = 'Control Entregas Ensamble Externo';

        $pdf = $this->initMPdf($title);
        $pdf->SetHTMLHeader(View::make('pdfs.Reports.header', compact('date', 'title')));
        $pdf->SetHTMLFooter(View::make('pdfs.Reports.footer'));
        $pdf->WriteHTML(View::make('pdfs.Reports.control_entregas_ensamble_externo', compact('data')), HTMLParserMode::HTML_BODY);
        $pdf->SetTitle("Ensamble Externo | EVPIU");

        $date = Carbon::now()->format('Y_m_d');

        return response()->make($pdf->Output("Pendientes_{$title}_{$date}", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @return Response
     */
    public function production_order_stock(): Response
    {
        $data = DB::connection('MAX')
            ->table('Order_Master')
            ->join('Part_Master', 'PRTNUM_10', '=', 'PRTNUM_01')
            ->select(DB::raw("ORDNUM_10 AS OP, RTRIM(PRTNUM_10) AS CODE, RTRIM(PMDES1_01) AS DESCRIPTION,
                FORMAT(CURDUE_10, 'yyyy-MM-dd') AS DATE, STATUS_10 AS STATE_OP, RTRIM(ORDREF_10) AS DESCRIPTION_OP,
              RTRIM(LOTNUM_10) AS LOT, CAST(CURQTY_10 AS INT) AS QUANTITY"))
            ->where('ORDREF_10', 'like', "%STOCK%")
            ->where('STATUS_10', '=', 3)
            ->orderBy('ORDNUM_10')
            ->get();

        return Inertia::render('Applications/Report/Production/ProductionOrderStock', [
            'data' => $data
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function production_order_stock_detail(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('JOB_PROGRESS')
                ->select('OPRDES_14', 'WRKCTR_14', 'QTYCOM_14', 'QTYREM_14', 'ASCRAP_14', 'CURSRT_14', 'MOVDTE_14', 'QUECDE_14', 'ORGCOM_14')
                ->where('ORDNUM_14', '=', str_pad($request->op, 12, '0'))
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $ct
     * @return Response
     */
    public function delivery_control_report($ct)
    {
        $title = match ($ct) {
            "CABEZ" => "Encabezados",
            "EAUT" => "Ensamble automatico",
            "TROQL" => "Broches",
            "DEFOR" => "Deformadora de alambre",
            "PLANT" => "Galvano 1",
            "GALV2" => "Galvano 2",
            "STAT" => "Estatica",
            "PINT" => "Pintura"
        };

        return Inertia::render('Applications/Report/Production/DeliveryControl', [
            'title' => $title,
            'ct' => $ct
        ]);
    }

    /**
     * @param $report
     * @param $type
     * @param Request $request
     * @return array|\Illuminate\Http\Response|mixed|BinaryFileResponse
     */
    public function delivery_control_report_pdf($report, $type, Request $request)
    {
        try {
            $date = explode(' - ', $request->date_range);

            $data = match ($report) {
                "CABEZ" => (object)[
                    "title" => "Encabezados",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD"
                    ]
                ],
                "EAUT" => (object)[
                    "title" => "Ensamble automatico",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD"
                    ]
                ],
                "TROQL" => (object)[
                    "title" => "Broches",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD"
                    ]
                ],
                "DEFOR" => (object)[
                    "title" => "Deformadora de alambre",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD"
                    ]
                ],
                "PLANT" => (object)[
                    "title" => "Galvano 1",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "COD_PROD" => "COD PRODUCTO",
                        "MATERIAL" => "MATERIAL",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD",
                        "RESPONSABLE" => "KILOS",
                        "FECHA" => "FECHA",
                        "ACABADO_GALV" => "ACABADO"
                    ]
                ],
                "GALV2" => (object)[
                    "title" => "Galvano 2",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "COD_PROD" => "COD PRODUCTO",
                        "MATERIAL" => "MATERIAL",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD",
                        "RESPONSABLE" => "KILOS",
                        "FECHA" => "FECHA",
                        "ACABADO_GALV" => "ACABADO"
                    ]
                ],
                "STAT" => (object)[
                    "title" => "Estatica",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD"
                    ]
                ],
                "PINT" => (object)[
                    "title" => "Pintura",
                    "columns" => (object)[
                        "OP" => "OP",
                        "REFERENCIA" => "REFERENCIA",
                        "PRODUCTO" => "PRODUCTO",
                        "ARTE" => "ARTE",
                        "CANT" => "CANTIDAD"
                    ]
                ]
            };

            $query = DB::connection('MAX')
                ->table($report === 'CABEZ' ? 'CIEV_V_ControlEntregasEncabezado' : 'CIEV_V_ControlEntregas')
                ->select(array_keys((array)$data->columns))
                ->where('CT', '=', $report)
                ->where(function($q) use($report){
                    if ($report === 'TROQL'){
                        $q->where('TIPO_PRODUCTO', '=', 'BROCHES')
                            ->where('OPERACION', '<=', '0440');
                    }
                })->whereBetween('FECHA', [
                    Carbon::parse($date[0])->format('Y-m-d'),
                    Carbon::parse($date[1])->format('Y-m-d')
                ])->get();

            $title = "CONTROL ENTREGAS {$data->title}";

            if ($type === 'pdf'){
                $pdf = $this->initMPdf($title);
                $pdf->SetHTMLHeader(View::make('pdfs.Reports.header', compact('date', 'title')));
                $pdf->SetHTMLFooter(View::make('pdfs.Reports.footer'));
                $pdf->WriteHTML(View::make('pdfs.Reports.delivery-control', compact('data', 'query')), HTMLParserMode::HTML_BODY);
                $pdf->SetTitle("$title | EVPIU");

                return response()->make($pdf->Output("Control-entregas-{$title}", 'I'), 200, [
                    'Content-Type' => 'application/pdf',
                ]);
            }else {
                return Excel::download(new DeliveryControlExport($title, $date, $data, $query), "$title.xlsx");
            }

        } catch (Exception $e) {
            return [
                "line" => $e->getLine(),
                "message" => $e->getMessage(),
                "file" => $e->getFile()
            ];
        }
    }

    /**
     * @return Response
     */
    public function monitoring_injection()
    {
        $rows = DB::connection('MAX')
            ->table('CIEV_V_SeguimientoInyeccion')
            ->get();

        return Inertia::render('Applications/Report/Production/MonitoringInjection', [
            'rows' => $rows,
        ]);
    }

    /**
     * @return BinaryFileResponse
     */
    public function download_report_monitoring_injection(Request $request)

    {
        $date = explode(' - ', $request->date_range);

        return Excel::download(new MonitoringInjectionExport($date), 'file.xlsx');
    }
}
