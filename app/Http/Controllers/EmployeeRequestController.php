<?php

namespace App\Http\Controllers;

use App\Mail\ApprovedVacationHumanResourceMail;
use App\Mail\JobLetterRequestMail;
use App\Mail\RefuseVacationByBossOrHumanResourceMail;
use App\Mail\VacationRequestMail;
use App\Models\JobLetterRequest;
use App\Models\Signature;
use App\Models\VacationRequest;
use App\Traits\PdfTrait;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Luecano\NumeroALetras\NumeroALetras;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use PHPUnit\Exception;

class EmployeeRequestController extends Controller
{
    use PdfTrait;

    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/EmployeeRequest/Index');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function view_pdf(): \Illuminate\Http\Response
    {
        $pdf = $this->working_letter(1214737718, 1128393000, 1, 1);

        return $pdf->stream();
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function info_employee(Request $request): JsonResponse
    {
        $data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', 'like', '%'.$request->q.'%')
            ->orWhere('nombres', 'like', '%'.$request->q.'%')
            ->where('estado', '=', 'A')
            ->get();

        return response()->json($data, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function save_request(Request $request): JsonResponse
    {
        $employee = JobLetterRequest::where('employee_document', '=', $request->employee_document)
            ->where('state', '=', '0')
            ->count();

        if ($employee > 0) {
            return response()->json('Usted ya tiene una solicitud pendiente', 422);
        } else {
            JobLetterRequest::create([
                'addressed_to' => $request->addressed_to,
                'employee_document' => $request->employee_document,
                'state' => '0',
            ]);

            return response()->json('ok', 200);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function save_vacation_request(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $employee = VacationRequest::where('employee_document', '=', $request->employee_document)
                ->where('state', '=', '0')
                ->count();

            if ($employee > 0) {
                return response()->json('Usted ya tiene una solicitud pendiente', 422);
            } else {
                Validator::make($request->all(), [
                    'employee_document' => 'required|integer',
                    'boss_document' => 'required|integer',
                    'date' => 'required',
                ])->validate();

                $date = explode(' - ', $request->date);

                $id = VacationRequest::create([
                    'employee_document' => $request->employee_document,
                    'boss_document' => $request->boss_document,
                    'start_date' => Carbon::parse($date[0]),
                    'end_date' => Carbon::parse($date[1]),
                    'justify' => $request->justify,
                    'state' => '0',
                ])->id;

                // variable que traiga informacion de la solicitud -> fecha de inicio / fin justificacion
                // info del jefe  -> correo el jefe
                // info del empleado -> nombre del empleado

                $boss_data = DB::connection('DMS')
                    ->table('V_CIEV_Personal')
                    ->where('nit', '=', $request->boss_document)
                    ->first();

                $employee_data = DB::connection('DMS')
                    ->table('V_CIEV_Personal')
                    ->where('nit', '=', $request->employee_document)
                    ->first();

                $registro = VacationRequest::find($id);

                $approved_url = url("/employee-requests/vacation-request/accept-request/{$request->boss_document}/{$id}/{$request->employee_document}");
                $refuse_url = url("/employee-requests/vacation-request/refuse-request/{$request->boss_document}/{$id}/{$request->employee_document}");

                Mail::to($boss_data->email_corporativo)
                    ->send(new VacationRequestMail($employee_data->nombres, $this->format_date(Carbon::parse($registro->start_date)), $this->format_date(Carbon::parse($registro->end_date)), $approved_url, $refuse_url, $request->justify));

                DB::commit();

                return response()->json('ok', 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $date
     * @return string
     */
    private function format_date($date): string
    {
        $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $month = $months[($date->format('n')) - 1];

        return $date->format('d').' de '.$month.' de '.$date->format('Y');
    }

    /**
     * @return Response
     */
    public function gestion_working_letter(): Response
    {
        $data = JobLetterRequest::with('employee')
            ->where('state', '=', '0')
            ->get();

        $signatures = Signature::all();

        return Inertia::render('Applications/EmployeeRequest/GestionWorkingLetter', [
            'data' => $data,
            'signatures' => $signatures,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refuse_working_letter(Request $request): JsonResponse
    {
        try {
            JobLetterRequest::find($request->id)
                ->update([
                    'state' => '2',
                    'observations' => $request->justify,
                ]);

            $data = JobLetterRequest::with('employee')
                ->where('state', '=', '0')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\Response|JsonResponse|void
     */
    public function approve_working_letter(Request $request)
    {
        DB::beginTransaction();
        try {
            $request_info = JobLetterRequest::find($request->id);
            $signature = Signature::find(intval($request->signature));

            $pdf = $this->working_letter($request_info->employee_document, $signature->document, $request->signature, $request->id);

            JobLetterRequest::find($request->id)
                ->update([
                    'state' => '1',
                    'observations' => '[SISTEMA] Carta laboral aprobada',
                    'approved_date' => Carbon::now(),
                    'approved_by' => auth()->user()->id,
                ]);

            switch ($request->type) {
                case 'mail':
                    $mail_address = DB::connection('DMS')
                        ->table('terceros')
                        ->where('nit', '=', $request_info->employee_document)
                        ->pluck('mail')
                        ->first();

                    Mail::to($mail_address)
                        ->send(new JobLetterRequestMail($pdf, 'carta_laboral.pdf'));

                    $data = JobLetterRequest::with('employee')
                        ->where('state', '=', '0')
                        ->get();
                    DB::commit();

                    return response()->json($data, 200);
                case 'download':
                    DB::commit();

                    return $pdf->download('file.pdf');
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function edit_working_letter(Request $request): JsonResponse
    {
        JobLetterRequest::find($request->id)
            ->update([
                'addressed_to' => $request->addressed_to,
            ]);

        $data = JobLetterRequest::with('employee')
            ->where('state', '=', '0')
            ->get();

        return response()->json($data, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function info_boss(Request $request): JsonResponse
    {
        $data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', 'like', '%'.$request->q.'%')
            ->orWhere('nombres', 'like', '%'.$request->q.'%')
            ->where('email_corporativo', '!=', null)
            ->get();

        return response()->json($data, 200);
    }

    /**
     * @param $boss_document
     * @param $request_id
     * @param $employee_document
     * @return Response
     */
    public function accept_vacation_request($boss_document, $request_id, $employee_document): Response
    {
        $data = VacationRequest::find($request_id);

        if ($data->state === '0') {
            $data->update([
                'state' => '1',
                'boss_approved_date' => Carbon::now(),
            ]);

            $employee_data = DB::connection('DMS')
                ->table('V_CIEV_Personal')
                ->where('nit', '=', $employee_document)
                ->first();

            return Inertia::render('Applications/EmployeeRequest/AcceptVacationRequest', [
                'data' => $data,
                'employee' => $employee_data,
            ]);
        } else {
            exit('solicitud no valida');
        }
    }

    /**
     * @param $boss_document
     * @param $request_id
     * @param $employee_document
     * @return Response
     */
    public function refuse_vacation_request($boss_document, $request_id, $employee_document): Response
    {
        $data = VacationRequest::find($request_id);

        $data->update([
            'state' => '2',
            'boss_approved_date' => Carbon::now(),
        ]);

        $employee_data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $employee_document)
            ->first();

        return Inertia::render('Applications/EmployeeRequest/RefuseVacationRequest', [
            'data' => $data,
            'employee' => $employee_data,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refuse_vacation_request_observation(Request $request): JsonResponse
    {
        $registro = VacationRequest::find($request->id);

        $registro->update([
            'state' => '2',
            'observations' => $request->observations,
        ]);

        $employee_data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $registro->employee_document)
            ->first();

        $boss_data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $registro->boss_document)
            ->first();

        Mail::to($employee_data->mail)
            ->send(new RefuseVacationByBossOrHumanResourceMail($boss_data->nombres, $request->observations));

        return response()->json('exito', 200);
    }

    /**
     * @return Response
     */
    public function vacation_request_human_resource(): Response
    {
        $data = VacationRequest::with('employee', 'boss')
            ->where('state', '=', '1')
            ->get();

        $finish = VacationRequest::with('employee', 'boss', 'approved_rrhh')
            ->where('state', '=', '3')
            ->get();

        return Inertia::render('Applications/EmployeeRequest/VacationRequestHumanResources', [
            'data' => $data,
            'finish' => $finish,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refuse_vacation_human_resource(Request $request): JsonResponse
    {
        $registro = VacationRequest::find($request->id);

        VacationRequest::find($request->id)->update([
            'state' => '4',
            'observations' => $request->observations,
        ]);

        $employee_data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $registro->employee_document)
            ->first();

        $data = VacationRequest::with('employee')
            ->where('state', '=', '1')
            ->get();

        Mail::to($employee_data->mail)
            ->send(new RefuseVacationByBossOrHumanResourceMail(auth()->user()->name, $request->observations));

        return response()->json($data, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function approve_vacation_human_resource(Request $request): JsonResponse
    {
        $registro = VacationRequest::find($request->id);

        VacationRequest::find($request->id)->update([
            'state' => '3',
            'approved_human_resource' => auth()->user()->id,
        ]);

        $employee_data = DB::connection('DMS')
            ->table('V_CIEV_Personal')
            ->where('nit', '=', $registro->employee_document)
            ->first();

        $data = VacationRequest::with('employee')
            ->where('state', '=', '1')
            ->get();

        if ($employee_data->mail){
            Mail::to($employee_data->mail)
                ->send(new ApprovedVacationHumanResourceMail($this->format_date(Carbon::parse($registro->start_date)), $this->format_date(Carbon::parse($registro->end_date)), auth()->user()->name));
        }

        return response()->json($data, 200);
    }

    /**
     * @param $id
     * @return string|void
     *
     * @throws MpdfException
     */
    public function print_pdf($id)
    {
        $data = VacationRequest::with('employee', 'boss', 'approved_rrhh')->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.vacation_request.header', compact('data')));
        $pdf->SetHTMLFooter(View::make('pdfs.vacation_request.footer', compact('data')));
        $pdf->WriteHTML(View::make('pdfs.vacation_request.template', compact('data')), HTMLParserMode::HTML_BODY);

        return \Illuminate\Support\Facades\Response::make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
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
            'format' => [215.9, 139.7],
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
            'margin_bottom' => 2,
            'margin_header' => 2,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/vacation_request/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function generate_working_letter(Request $request)
    {
        try {
            $pdf = $this->pdf_working_letter($request->employee, $request->to);

            return $pdf->download('file.pdf');
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $employee_document
     * @param $to
     * @return \Barryvdh\DomPDF\PDF|string
     */
    protected function pdf_working_letter($employee_document, $to)
    {
        try {
            $signature = Signature::first();

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
                ->where('nit', '=', $signature->document)
                ->where('estado', '=', 'A')
                ->first();

            $commissions = DB::connection('DMS')
                ->table('V_CIEV_Liquidaciones')
                ->where('CONCEPTO', '=', 80)
                ->where('IDENTIFICACION', '=', $employee_document)
                ->whereDate('FIN', '>', Carbon::now()->subMonths(1)->format('Y-m-d 00:00:00'))
                ->sum('VALOR');

            $salary = new NumeroALetras();
            $salary = $salary->toMoney($contract->basico_mes + $commissions, 2, 'PESOS', 'CENTAVOS');

            $imgBase64 = base64_encode(file_get_contents(storage_path($signature->path)));
            $signature_image = 'data:image/png;base64,'.$imgBase64;

            $request_info = (object) [
                'addressed_to' => $to,
            ];

            $pdf = PDF::loadView('working_letter',
                compact('contract', 'employee_info', 'salary',
                    'approve_user_info', 'signature_image', 'request_info', 'commissions')
            );

            $pdf->setWarnings(false);
            $pdf->setPaper('letter', 'portrait');

            return $pdf;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
