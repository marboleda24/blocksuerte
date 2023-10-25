<?php

namespace App\Http\Controllers;

use App\Jobs\SendProofPaymentMailQueueEmail;
use App\Models\Signature;
use App\Traits\PdfTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use PHPUnit\Exception;

class HumanResourceController extends Controller
{
    /**
     * PDF Generator
     */
    use PdfTrait;

    /**
     * @return Response
     */
    public function index(): Response
    {
        $years = DB::connection('DMS')
            ->table('y_periodos_control')
            ->select('ano')
            ->groupBy('ano')
            ->orderBy('ano', 'desc')
            ->take(2)
            ->get();

        return Inertia::render('Applications/HumanResource/ProofPayment', [
            'years' => $years,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_employee(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('DMS')
                ->table('V_CIEV_Personal')
                ->where('estado', '=', 'A')
                ->where(function ($query) use ($request) {
                    $query->where('nit', 'like', '%'.$request->q.'%')
                        ->orWhere('nombres', 'like', '%'.$request->q.'%');
                })->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_months(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('DMS')
                ->table('y_periodos_control')
                ->select('mes')
                ->where('ano', '=', $request->year)
                ->where('acumulado', '=', true)
                ->orderBy('mes')
                ->groupBy('mes')
                ->get()
                ->toArray();

            $months_table = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre',
            ];

            $v = array_reduce($data, function ($result, $item) use ($months_table) {
                $result[$item->mes] = [
                    'month' => $item->mes,
                    'name' => $months_table[$item->mes],
                ];

                return $result;
            }, []);

            return response()->json($v, 200);
        } catch (\Exception $e) {
            return response()->json($e->getLine(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function get_periods(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('DMS')
                ->table('y_periodos_control')
                ->select('ano', 'mes', 'periodo', 'fecha_inicial', 'fecha_final', 'notas')
                ->where('ano', '=', $request->year)
                ->where('mes', '=', $request->month)
                ->where('acumulado', '=', true)
                ->orderBy('mes')
                ->get()
                ->toArray();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $req
     * @return JsonResponse
     */
    public function send_mail(Request $req): JsonResponse
    {
        set_time_limit(0);

        if ($req->type === 'unique') {
            try {
                $filename = $req->employee_nit.'_'.$req->year.'_'.$req->month.'_'.$req->period.'.pdf';

                $mail_address = DB::connection('DMS')
                    ->table('terceros')
                    ->where('nit', '=', $req->employee_nit)
                    ->pluck('mail')
                    ->first();

                $pdf_data = [
                    'nit' => $req->employee_nit,
                    'year' => $req->year,
                    'month' => $req->month,
                    'period' => $req->period,
                    'period_str' => $req->period_str,
                    'filename' => $filename,
                    'mail_address' => $mail_address,
                ];

                $job = (new SendProofPaymentMailQueueEmail($pdf_data))
                    ->delay(now()->addSeconds(2));

                dispatch($job);

                return response()->json('mail sent successfully', 200);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        } elseif ($req->type === 'massive') {
            try {
                $employees = DB::connection('DMS')
                    ->table('V_CIEV_EmpleadosPeriodo')
                    ->where('ano', '=', $req->year)
                    ->where('mes', '=', $req->month)
                    ->where('periodo', '=', $req->period)
                    ->get();

                foreach ($employees as $employee) {
                    $filename = $employee->nit.'_'.$req->year.'_'.$req->month.'_'.$req->period.'.pdf';

                    if ($employee->mail) {
                        $pdf_data = [
                            'nit' => $employee->nit,
                            'year' => $req->year,
                            'month' => $req->month,
                            'period' => $req->period,
                            'period_str' => $req->period_str,
                            'filename' => $filename,
                            'mail_address' => $employee->mail,
                        ];

                        $job = (new SendProofPaymentMailQueueEmail($pdf_data))
                            ->delay(now()->addSeconds(2));

                        dispatch($job);

                        Log::info('['.$employee->mail.']: mail sent');
                    }
                }

                return response()->json('massive mail sent successfully', 200);
            } catch (\Exception $e) {
                return response()->json("[{$e->getFile()}][{$e->getLine()}]{$e->getMessage()}", 500);
            }
        }
    }

    /**
     * @param  Request  $req
     * @return string
     *
     * @throws MpdfException
     */
    public function download(Request $req)
    {
        $pdf = $this->createPDF($req->employee_nit, $req->year, $req->month, $req->period, $req->period_str);
        $filename = $req->employee_nit.'_'.$req->year.'_'.$req->month.'_'.$req->period.'.pdf';

        return $pdf->Output($filename, 'D');
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
        $pdf->SetProtection(array('print'));
        $pdf->SetTitle("Comprobante de nomina");
        $pdf->SetAuthor("CI ESTRADA VELASQUEZ & CIA SAS");

        return $pdf;
    }

    /**
     * @return Response
     */
    public function peace_safe(): Response
    {
        $signatures = Signature::all();

        return Inertia::render('Applications/HumanResource/PeaceSafe', [
            'signatures' => $signatures,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_retired_employee(Request $request)
    {
        try {
            $query = $request->get('q');

            $results = DB::connection('DMS')
                ->table('V_CIEV_EmpleadosCentroCostos')
                ->select('nit', 'nombres_apellidos')
                ->where('estado', '=', 'R')
                ->where('nit', 'like', "%{$query}%")
                ->orWhere('nombres_apellidos', 'like', "%{$query}%")
                ->distinct()
                ->get()->map(function ($row) {
                    return [
                        'document' => $row->nit,
                        'name' => $row->nombres_apellidos,
                        'active' => DB::connection('DMS')
                            ->table('y_personal_contratos')
                            ->where('nit', '=', $row->nit)
                            ->where('estado', '=', 'A')
                            ->count(),
                    ];
                })->where('active', '=', 0);

            $data = [];
            foreach ($results as $key => $value) {
                $data[] = $value;
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function peace_safe_generate_document(Request $request): \Illuminate\Http\Response
    {
        $signature = Signature::find(intval($request->signature));

        $pdf = $this->retired_employee_pdf($request->document, $signature->document, $request->signature);

        return $pdf->download('file.pdf');
    }
}
