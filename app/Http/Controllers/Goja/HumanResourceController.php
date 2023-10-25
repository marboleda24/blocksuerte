<?php

namespace App\Http\Controllers\Goja;

use App\Http\Controllers\Controller;
use App\Jobs\SendProofPaymentMailQueueEmailGoja;
use App\Traits\GojaPdfTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\MpdfException;
use PHPUnit\Exception;

class HumanResourceController extends Controller
{
    /**
     * PDF Generator
     */
    use GojaPdfTrait;

    /**
     * @return Response
     */
    public function index(): Response
    {
        $years = DB::connection('GOJA')
            ->table('y_periodos_control')
            ->select('ano')
            ->groupBy('ano')
            ->orderBy('ano', 'desc')
            ->take(2)
            ->get();

        return Inertia::render('Applications/Goja/ProofPayment', [
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
            $data = DB::connection('GOJA')
                ->table('v_Personal')
                ->where('estado', '=', 'A')
                ->where(function ($query) use ($request) {
                    $query->where('nit', 'like', '%'.$request->q.'%')
                        ->orWhere('Nombres1', 'like', '%'.$request->q.'%');
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
            $data = DB::connection('GOJA')
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
            $data = DB::connection('GOJA')
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
        if ($req->type === 'unique') {
            try {
                $filename = $req->employee_nit.'_'.$req->year.'_'.$req->month.'_'.$req->period.'.pdf';

                $mail_address = DB::connection('GOJA')
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

                $job = (new SendProofPaymentMailQueueEmailGoja($pdf_data))
                    ->delay(now()->addSeconds(2));

                dispatch($job);

                return response()->json('mail sent successfully', 200);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        } elseif ($req->type === 'massive') {
            try {
                $employees = DB::connection('GOJA')
                    ->table('v_EmpleadosPeriodo')
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

                        $job = (new SendProofPaymentMailQueueEmailGoja($pdf_data))
                            ->delay(now()->addSeconds(2));

                        dispatch($job);

                        Log::info('['.$employee->mail.']: mail sent');
                    }
                }

                return response()->json('massive mail sent successfully', 200);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    /**
     * @param  Request  $req
     * @return string|void
     *
     * @throws MpdfException
     */
    public function download(Request $req)
    {
        $pdf = $this->createPDF($req->employee_nit, $req->year, $req->month, $req->period, $req->period_str);
        $filename = $req->employee_nit.'_'.$req->year.'_'.$req->month.'_'.$req->period.'.pdf';

        return $pdf->Output($filename, 'D');
    }
}
