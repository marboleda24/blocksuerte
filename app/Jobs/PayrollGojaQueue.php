<?php

namespace App\Jobs;

use App\Models\Dian\PayrollDocument;
use App\Models\PayrollLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class PayrollGojaQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $employee_document;

    public $year;

    public $month;

    public $start_period;

    public $user_id;

    public $end_period;

    public $type_operation;

    public $document_reference;

    public $response_api;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $employee_document,
        $year,
        $month,
        $start_period,
        $end_period,
        $type_operation,
        $document_reference, $user_id)
    {
        $this->employee_document = $employee_document;
        $this->year = $year;
        $this->month = $month;
        $this->start_period = $start_period;
        $this->end_period = $end_period;
        $this->type_operation = $type_operation;
        $this->document_reference = $document_reference;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws GuzzleException
     */
    public function handle()
    {
        $token = config('apidian.token_goja');
        $url = config('apidian.url');
        set_time_limit(0);

        $client = new Client(['base_uri' => $url]);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        switch ($this->type_operation) {
            case 'adjust':
                $params = $this->generateParams(
                    $this->employee_document,
                    $this->year,
                    $this->month,
                    $this->start_period,
                    $this->end_period,
                    $this->document_reference
                );

                $this->response_api = $client->request('POST', "{$url}/ubl2.1/payroll-adjust-note", [
                    'headers' => $headers,
                    'json' => $params,
                ]);

                break;
            case 'payroll':
                $params = $this->generateParams(
                    $this->employee_document,
                    $this->year,
                    $this->month,
                    $this->start_period,
                    $this->end_period,
                );

                $this->response_api = $client->request('POST', "{$url}/ubl2.1/payroll", [
                    'headers' => $headers,
                    'json' => $params,
                ]);
                break;
            case 'destroy':
                $params = $this->generateParamsDestroy($this->document_reference);

                $this->response_api = $client->request('POST', "{$url}/ubl2.1/payroll-adjust-note", [
                    'headers' => $headers,
                    'json' => $params,
                ]);
                break;
        }

        $resp = json_decode($this->response_api->getBody()->getContents());

        PayrollLog::create([
            'consecutive' => $params['consecutive'],
            'status' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid,
            'statusCode' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusCode,
            'ErrorMessage' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string ?? '',
            'cune' => $resp->cune,
            'send_by' => $this->user_id,
            'entity' => 'GOJA',
        ]);
    }

    /**
     * @param $document
     * @param $year
     * @param $month
     * @param $start_period
     * @param $end_period
     * @param  null  $document_reference
     * @return array
     *
     * @throws GuzzleException
     */
    protected function generateParams($document, $year, $month, $start_period, $end_period, $document_reference = null): array
    {
        $employee = DB::connection('GOJA')
            ->table('terceros_nombres')
            ->where('nit', '=', $document)
            ->first();

        $other_info = DB::connection('GOJA')
            ->table('terceros')
            ->where('nit', '=', $document)
            ->first();

        $pay = DB::connection('GOJA')
            ->table('v_Liquidaciones')
            ->where('IDENTIFICACION', '=', $document)
            ->where('AÑO', '=', $year)
            ->where('MES', '=', $month)
            ->whereBetween('PERIODO', [$start_period, $end_period])
            ->selectRaw('EMPLEADO, CONCEPTO, DESCRIPCION_CONCEPTO, SUM(VALOR) as VALOR, SUM(HORAS) as HORAS, SUM(DEVENGADO) as DEVENGADO, SUM(PAGO) as PAGO, SUM(DEDUCCIONES) as DEDUCCIONES')
            ->groupBy('EMPLEADO', 'CONCEPTO', 'DESCRIPCION_CONCEPTO')
            ->get();

        $contract = DB::connection('GOJA')
            ->table('v_NomContratos')
            ->where('nit', '=', $document)
            ->where('ESTADO', '=', 'ACTIVO')
            ->first();

        $city = DB::connection('API_DIAN')
            ->table('municipalities')
            ->where('name', 'like', "%$other_info->ciudad%")
            ->orWhere('code', '=', "{$other_info->y_dpto}{$other_info->y_ciudad}")
            ->first();

        $doc_ref = PayrollDocument::where('state_document_id', '=', 1)
            ->where('consecutive', '=', $document_reference)
            ->first();

        $params = [
            'type_document_id' => $doc_ref ? 10 : 9,
            'year' => $year,
            'month' => $month,
            'novelty' => [
                'novelty' => false,
                'uuidnov' => '',
            ],
            'period' => [
                'admision_date' => Carbon::parse($contract->fecha_ingreso)->format('Y-m-d'),
                'settlement_start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'settlement_end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'worked_time' => cal_days_in_month(CAL_GREGORIAN, $month, $year),
                'issue_date' => Carbon::now()->format('Y-m-d'),
            ],
            'worker_code' => (string) $employee->nit,
            'prefix' => 'NI',
            'consecutive' => $this->last_consecutive(9, 'NI'),
            'payroll_period_id' => 4,
            'worker' => [
                'type_worker_id' => 1,
                'sub_type_worker_id' => 1,
                'payroll_type_document_identification_id' => 3,
                'municipality_id' => $city->id,
                'type_contract_id' => $contract->TIPO_CONTRATO === 'INDEFINIDO' ? 2 : ($contract->TIPO_CONTRATO === 'APRENDIZ' ? 4 : 1),
                'high_risk_pension' => false,
                'identification_number' => $employee->nit,
                'surname' => $employee->primer_apellido,
                'second_surname' => $employee->segundo_apellido ?? '',
                'first_name' => $employee->primer_nombre,
                'middle_name' => $employee->segundo_nombre,
                'address' => $other_info->direccion,
                'integral_salarary' => false,
                'salary' => number_format($contract->basico_mes, 2, '.', ''),
            ],
            'payment' => [
                'payment_method_id' => 45,
                'bank_name' => 'BANCOLOMBIA',
                'account_type' => 'AHORROS',
                'account_number' => $contract->cta_empleado,
            ],
            'payment_dates' => [
                ['payment_date' => Carbon::parse("{$year}-{$month}-15")->format('Y-m-15')],
                ['payment_date' => Carbon::parse("$year-$month-01")->format('Y-m-t')],
            ],
            'accrued' => [
                'worked_days' => cal_days_in_month(CAL_GREGORIAN, $month, $year),
                'salary' => number_format($pay->whereIn('CONCEPTO', [15, 1, 3, 6])->sum('PAGO') ?? 0, 2, '.', ''),
                'accrued_total' => number_format($pay->sum('PAGO'), 2, '.', ''),
            ],
            'deductions' => [
                'eps_type_law_deductions_id' => 1,
                'eps_deduction' => $pay->where('CONCEPTO', '=', 301)->sum('DEDUCCIONES') ?? 0,
                'pension_type_law_deductions_id' => 5,
                'pension_deduction' => $pay->where('CONCEPTO', '=', 302)->sum('DEDUCCIONES') ?? 0,
                'deductions_total' => number_format($pay->sum('DEDUCCIONES') ?? 0, 2, '.', ''),
            ],
        ];

        $transportation_allowance = $pay->whereIn('CONCEPTO', [60, 62]);
        $endowment = $pay->where('CONCEPTO', '=', 70);
        $heds = $pay->where('CONCEPTO', '=', 31);
        $hens = $pay->where('CONCEPTO', '=', 32);
        $hrns = $pay->where('CONCEPTO', '=', 2);
        $heddfs = $pay->where('CONCEPTO', '=', 21);
        $hrddfs = $pay->where('CONCEPTO', '=', 41);
        $hendfs = $pay->where('CONCEPTO', '=', 22);
        $hrndfs = $pay->where('CONCEPTO', '=', 42);
        $common_vacation = $pay->where('CONCEPTO', '=', 101);
        $paid_vacation = $pay->where('CONCEPTO', '=', 103);
        $service_bonus = $pay->where('CONCEPTO', '=', 100);
        $severance = $pay->where('CONCEPTO', '=', 105);
        $work_disabilities = $pay->where('CONCEPTO', '=', 54);
        $maternity_leave = $pay->where('CONCEPTO', '=', 53);
        $paid_leave = $pay->where('CONCEPTO', '=', 17);
        $non_paid_leave = $pay->where('CONCEPTO', '=', 18);
        $bonuses = $pay->whereIn('CONCEPTO', [76, 78]);
        $aid = $pay->where('CONCEPTO', '=', 62);
        $commissions = $pay->where('CONCEPTO', '=', 80);
        $third_party_payments = $pay->where('CONCEPTO', '=', 77);
        $sustenance_support = $pay->where('CONCEPTO', '=', 85);
        $compensation = $pay->where('CONCEPTO', '=', 115);
        $refund = $pay->where('CONCEPTO', '=', 75);

        if ($doc_ref) {
            $params['type_note'] = 1;
            $params['period']['retirement_date'] = Carbon::parse($doc_ref->date_issue)->format('Y-m-d');
            $params['predecessor'] = [
                'predecessor_number' => (int) $doc_ref->consecutive,
                'predecessor_cune' => $doc_ref->cune,
                'predecessor_issue_date' => Carbon::parse($doc_ref->date_issue)->format('Y-m-d'),
            ];
        }

        if (count($transportation_allowance) > 0) {
            $params['accrued']['transportation_allowance'] = $transportation_allowance->sum('PAGO');
        }

        if (count($heds) > 0) {
            $params['accrued']['HEDs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $heds->sum('HORAS') ?? 0,
                'percentage' => 1,
                'payment' => $heds->sum('PAGO') ?? 0,
            ];
        }

        if (count($hens) > 0) {
            $params['accrued']['HENs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $hens->sum('HORAS') ?? 0,
                'percentage' => 2,
                'payment' => $hens->sum('PAGO') ?? 0,
            ];
        }

        if (count($hrns) > 0) {
            $params['accrued']['HRNs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $hrns->sum('HORAS') ?? 0,
                'percentage' => 3,
                'payment' => $hrns->sum('PAGO') ?? 0,
            ];
        }

        if (count($heddfs) > 0) {
            $params['accrued']['HEDDFs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $heddfs->sum('HORAS') ?? 0,
                'percentage' => 4,
                'payment' => $heddfs->sum('PAGO') ?? 0,
            ];
        }

        if (count($hrddfs) > 0) {
            $params['accrued']['HRDDFs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $hrddfs->sum('HORAS') ?? 0,
                'percentage' => 5,
                'payment' => $hrddfs->sum('PAGO') ?? 0,
            ];
        }

        if (count($hendfs) > 0) {
            $params['accrued']['HENDFs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $hendfs->sum('HORAS') ?? 0,
                'percentage' => 6,
                'payment' => $hendfs->sum('PAGO') ?? 0,
            ];
        }

        if (count($hrndfs) > 0) {
            $params['accrued']['HRNDFs'][] = [
                'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                'quantity' => $hrndfs->sum('HORAS') ?? 0,
                'percentage' => 7,
                'payment' => $hrndfs->sum('PAGO') ?? 0,
            ];
        }

        if (count($common_vacation) > 0) {
            $params['accrued']['common_vacation'][] = [
                'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'quantity' => $common_vacation->sum('HORAS') ?? 0,
                'payment' => $common_vacation->sum('PAGO') ?? 0,
            ];
        }

        if (count($paid_vacation) > 0) {
            $params['accrued']['paid_vacation'][] = [
                'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'quantity' => $paid_vacation->sum('HORAS') ?? 0,
                'payment' => $paid_vacation->sum('PAGO') ?? 0,
            ];
        }

        if (count($service_bonus) > 0) {
            $params['accrued']['service_bonus'][] = [
                'quantity' => $service_bonus->sum('HORAS') ?? 0,
                'payment' => $service_bonus->sum('PAGO') ?? 0,
                'paymentNS' => '00.00',
            ];
        }

        if (count($severance) > 0) {
            $params['accrued']['severance'][] = [
                'payment' => $severance->sum('PAGO') ?? 0,
                'percentage' => $pay->where('CONCEPTO', '=', 110)->sum('HORAS') ?? 0,
                'interest_payment' => $pay->where('CONCEPTO', '=', 110)->sum('PAGO') ?? 0,
            ];
        }

        if (count($work_disabilities) > 0) {
            $params['accrued']['work_disabilities'][] = [
                'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'type' => 3,
                'quantity' => $work_disabilities->sum('HORAS') ? $work_disabilities->sum('HORAS') / 8 : 0,
                'payment' => $work_disabilities->sum('PAGO') ?? 0,
            ];
        }

        if (count($maternity_leave) > 0) {
            $params['accrued']['maternity_leave'][] = [
                'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'quantity' => $maternity_leave->sum('HORAS') ? $maternity_leave->sum('HORAS') / 8 : 0,
                'payment' => $maternity_leave->sum('PAGO') ?? 0,
            ];
        }

        if (count($paid_leave) > 0) {
            $params['accrued']['paid_leave'][] = [
                'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'quantity' => $paid_leave->sum('HORAS') ? $paid_leave->sum('HORAS') / 8 : 0,
                'payment' => $paid_leave->sum('PAGO') ?? 0,
            ];
        }

        if (count($non_paid_leave) > 0) {
            $params['accrued']['non_paid_leave'][] = [
                'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                'quantity' => $non_paid_leave->sum('HORAS') ? $non_paid_leave->sum('HORAS') / 8 : 0,
            ];
        }

        if (count($aid) > 0) {
            $params['accrued']['aid'][] = [
                'salary_assistance' => $aid->sum('PAGO') ?? 0,
            ];
        }

        if (count($bonuses) > 0) {
            if ($pay->where('CONCEPTO', '=', 78)->count() > 0) {
                $params['accrued']['bonuses'][] = [
                    'salary_bonus' => $pay->where('CONCEPTO', '=', 78)->sum('PAGO') ?? 0,
                ];
            } elseif ($pay->where('CONCEPTO', '=', 76)->count() > 0) {
                $params['accrued']['bonuses'][] = [
                    'non_salary_bonus' => $pay->where('CONCEPTO', '=', 76)->sum('PAGO') ?? 0,
                ];
            }
        }

        if (count($commissions) > 0) {
            $params['accrued']['commissions'][] = [
                'commission' => $commissions->sum('PAGO') ?? 0,
            ];
        }

        if (count($third_party_payments) > 0) {
            $params['accrued']['third_party_payments'][] = [
                'third_party_payment' => $third_party_payments->sum('PAGO') ?? 0,
            ];
        }

        if (count($endowment) > 0) {
            $params['accrued']['endowment'] = number_format($endowment->sum('PAGO') ?? 0, 2, '.', '');
        }

        if (count($sustenance_support) > 0) {
            $params['accrued']['sustenance_support'] = number_format($sustenance_support->sum('PAGO') ?? 0, 2, '.', '');
        }

        if (count($compensation) > 0) {
            $params['accrued']['compensation'] = number_format($compensation->sum('PAGO') ?? 0, 2, '.', '');
        }

        if (count($refund) > 0) {
            $params['accrued']['refund'] = number_format($refund->sum('PAGO'), 2, '.', '');
        }

        /**
         * DEDUCCCIONES
         */
        $orders = $pay->whereIn('CONCEPTO', [436, 437, 438, 441, 443]);
        $sanctions = $pay->where('CONCEPTO', '=', 350);
        $third_party_payments = $pay->whereIn('CONCEPTO', [439, 442]);
        $other_deductions = $pay->where('CONCEPTO', '=', 350);
        $voluntary_pension = $pay->where('CONCEPTO', '=', 310);
        $withholding_at_source = $pay->where('CONCEPTO', '=', 310);
        $tax_liens = $pay->whereIn('CONCEPTO', [320, 321, 322]);
        $debt = $pay->whereIn('CONCEPTO', [152, 153]);

        foreach ($orders as $order) {
            $params['deductions']['orders'][] = [
                'description' => $order->DESCRIPCION_CONCEPTO,
                'deduction' => $order->DEDUCCIONES,
            ];
        }

        if (count($sanctions) > 0) {
            $params['deductions']['sanctions'][] = [
                'private_sanction' => $sanctions->sum('DEDUCCIONES') ?? 0,
            ];
        }

        if (count($third_party_payments) > 0) {
            $params['deductions']['third_party_payments'][] = [
                'third_party_payment' => $third_party_payments->sum('DEDUCCIONES') ?? 0,
            ];
        }

        if (count($other_deductions) > 0) {
            $params['deductions']['other_deductions'][] = [
                'other_deduction' => $other_deductions->sum('DEDUCCIONES') ?? 0,
            ];
        }

        if (count($voluntary_pension) > 0) {
            $params['deductions']['voluntary_pension'] = $voluntary_pension->sum('DEDUCCIONES') ?? 0;
        }

        if (count($withholding_at_source) > 0) {
            $params['deductions']['withholding_at_source'] = $withholding_at_source->sum('DEDUCCIONES') ?? 0;
        }

        if (count($tax_liens) > 0) {
            $params['deductions']['tax_liens'] = $tax_liens->sum('DEDUCCIONES') ?? 0;
        }

        if (count($debt) > 0) {
            $params['deductions']['debt'] = $debt->sum('DEDUCCIONES') ?? 0;
        }

        return $params;
    }

    /**
     * @param $document_reference
     * @return array
     *
     * @throws GuzzleException
     */
    protected function generateParamsDestroy($document_reference): array
    {
        $doc = PayrollDocument::where('state_document_id', '=', 1)
            ->where('consecutive', '=', $document_reference)
            ->first();

        return [
            'type_document_id' => 10,
            'type_note' => 2,
            'predecessor' => [
                'predecessor_number' => $doc->consecutive,
                'predecessor_cune' => $doc->cune,
                'predecessor_issue_date' => Carbon::parse($doc->date_issue)->format('Y-m-d'),
            ],
            'prefix' => 'NI',
            'consecutive' => $this->last_consecutive(9, 'NI'),
            'payroll_period_id' => 4,
            'notes' => 'PRUEBA DE ENVIO DE NOMINA DE AJUSTE ELECTRONICA - ELIMINAR',
        ];
    }

    /**
     * @param $type
     * @param $prefix
     * @return mixed
     *
     * @throws GuzzleException
     */
    protected function last_consecutive($type, $prefix)
    {
        $token = config('apidian.token_goja');
        $url = config('apidian.url');

        $client = new Client(['base_uri' => 'http://apidian-3.test/api/']);

        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        $response = $client->request('GET', "{$url}/ubl2.1/invoice/current_number/{$type}/{$prefix}", [
            'headers' => $headers,
        ]);

        $result = json_decode($response->getBody()->getContents());

        return $result->number;
    }

    /**
     * @param  string  $type
     * @return int
     */
    protected function type_document(string $type): int
    {
        switch (trim($type)) {
            case 'C':
                return 1;
            case 'E':
                return 2;
            case 'T':
                return 3;
            case 'P':
                return 4;
            case 'PE':
                return 9;
        }

        return false;
    }
}
