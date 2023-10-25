<?php

namespace App\Traits;

use App\Models\Dian\PayrollDocument;
use App\Models\PayrollLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait PayrollTrait
{
    /**
     * @param $document
     * @param $year
     * @param $month
     * @param $start_period
     * @param $end_period
     * @param  null  $document_reference
     * @param  string  $entity
     * @return array
     */
    protected function generateParams($document, $year, $month, $start_period, $end_period, $document_reference = null, string $entity = 'CIEV'): array
    {
        try {
            $employee = DB::connection($entity === 'CIEV' ? 'DMS' : 'GOJA')
                ->table('terceros_nombres')
                ->where('nit', '=', $document)
                ->first();

            $other_info = DB::connection($entity === 'CIEV' ? 'DMS' : 'GOJA')
                ->table('terceros')
                ->where('nit', '=', $document)
                ->first();

            $pay = DB::connection($entity === 'CIEV' ? 'DMS' : 'GOJA')
                ->table($entity === 'CIEV' ? 'V_CIEV_Liquidaciones' : 'v_Liquidaciones')
                ->where('IDENTIFICACION', '=', $document)
                ->where('AÑO', '=', $year)
                ->where('MES', '=', $month)
                ->whereBetween('PERIODO', [$start_period, $end_period])
                ->selectRaw('EMPLEADO, contrato, CONCEPTO, DESCRIPCION_CONCEPTO, SUM(VALOR) as VALOR, SUM(HORAS) as HORAS, SUM(DEVENGADO) as DEVENGADO, SUM(PAGO) as PAGO, SUM(DEDUCCIONES) as DEDUCCIONES')
                ->groupBy('EMPLEADO', 'contrato', 'CONCEPTO', 'DESCRIPCION_CONCEPTO')
                ->get();

            $contract = DB::connection($entity === 'CIEV' ? 'DMS' : 'GOJA')
                ->table($entity === 'CIEV' ? 'V_CIEV_NomContratos' : 'v_NomContratos')
                ->where('nit', '=', $document)
                ->where('contrato', '=', $pay[0]->contrato)
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
                    'admission_date' => Carbon::parse($contract->fecha_ingreso)->format('Y-m-d'),
                    'settlement_start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'settlement_end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'worked_time' => cal_days_in_month(CAL_GREGORIAN, $month, $year),
                    'issue_date' => Carbon::now()->format('Y-m-d'),
                ],
                'worker_code' => (string) $employee->nit,
                'prefix' => 'NI',
                'consecutive' => $this->last_consecutive($entity),
                'payroll_period_id' => 4,
                'worker' => [
                    'type_worker_id' => 1,
                    'sub_type_worker_id' => 1,
                    'payroll_type_document_identification_id' => $this->type_document($other_info->tipo_identificacion),
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
                    'salary' => number_format($pay->whereIn('CONCEPTO', [15, 1, 3, 5, 6])->sum('PAGO') ?? 0, 2, '.', ''),
                    'accrued_total' => number_format($pay->whereNotIn('CONCEPTO', [14, 110, 101, 102, 103, 105])->sum('PAGO'), 2, '.', ''),
                ],
                'deductions' => [
                    'eps_type_law_deductions_id' => 1,
                    'eps_deduction' => $pay->where('CONCEPTO', '=', 301)->sum('DEDUCCIONES') ?? 0,
                    'pension_type_law_deductions_id' => 5,
                    'pension_deduction' => $pay->where('CONCEPTO', '=', 302)->sum('DEDUCCIONES') ?? 0,
                    'deductions_total' => number_format($pay->sum('DEDUCCIONES') ?? 0, 2, '.', ''),
                ],
            ];

            $transportation_allowance = $pay->whereIn('CONCEPTO', [60, 61, 62]);
            $endowment = $pay->where('CONCEPTO', '=', 70);
            $heds = $pay->whereIn('CONCEPTO', [4, 31]);
            $hens = $pay->where('CONCEPTO', '=', 32);
            $hrns = $pay->where('CONCEPTO', '=', 2); // recargo nocturno
            $heddfs = $pay->where('CONCEPTO', '=', 21);
            $hrddfs = $pay->where('CONCEPTO', '=', 12);
            $hendfs = $pay->where('CONCEPTO', '=', 22);
            $hrndfs = $pay->where('CONCEPTO', '=', 42);
            //$common_vacation = $pay->whereIn('CONCEPTO', [101, 102, 14]);
            //$paid_vacation = $pay->where('CONCEPTO', '=', 103);
            $service_bonus = $pay->where('CONCEPTO', '=', 100);
            //$severance = $pay->where('CONCEPTO', '=', 105);
            $work_disabilities = $pay->whereIn('CONCEPTO', [19, 46, 47, 48, 49, 50, 51, 52, 54]);
            $maternity_leave = $pay->whereIn('CONCEPTO',[53, 55]);
            $paid_leave = $pay->where('CONCEPTO', '=', 17);
            $non_paid_leave = $pay->where('CONCEPTO', '=', 18);
            $bonuses = $pay->whereIn('CONCEPTO', [63, 76, 78]);
            $aid = $pay->where('CONCEPTO', '=', 62);
            $commissions = $pay->whereIn('CONCEPTO', [80, 81]);
            $third_party_payments = $pay->where('CONCEPTO', '=', 77);
            $sustenance_support = $pay->where('CONCEPTO', '=', 85);
            $compensation = $pay->where('CONCEPTO', '=', 115);
            $refund = $pay->whereIn('CONCEPTO',[7, 75]);

            if ($doc_ref) {
                $params['type_note'] = 1;
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
                    'quantity' => (int) $heds->sum('HORAS') ?? 0,
                    'percentage' => 1,
                    'payment' => $heds->sum('PAGO') ?? 0,
                ];
            }

            if (count($hens) > 0) {
                $params['accrued']['HENs'][] = [
                    'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                    'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                    'quantity' => (int) $hens->sum('HORAS') ?? 0,
                    'percentage' => 2,
                    'payment' => $hens->sum('PAGO') ?? 0,
                ];
            }

            if (count($hrns) > 0) {
                $params['accrued']['HRNs'][] = [
                    'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                    'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                    'quantity' => (int) $hrns->sum('HORAS') ?? 0,
                    'percentage' => 3,
                    'payment' => $hrns->sum('PAGO') ?? 0,
                ];
            }

            if (count($heddfs) > 0) {
                $params['accrued']['HEDDFs'][] = [
                    'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                    'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                    'quantity' => (int) $heddfs->sum('HORAS') ?? 0,
                    'percentage' => 4,
                    'payment' => $heddfs->sum('PAGO') ?? 0,
                ];
            }

            if (count($hrddfs) > 0) {
                $params['accrued']['HRDDFs'][] = [
                    'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                    'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                    'quantity' => (int) $hrddfs->sum('HORAS') ?? 0,
                    'percentage' => 5,
                    'payment' => $hrddfs->sum('PAGO') ?? 0,
                ];
            }

            if (count($hendfs) > 0) {
                $params['accrued']['HENDFs'][] = [
                    'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                    'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                    'quantity' => (int) $hendfs->sum('HORAS') ?? 0,
                    'percentage' => 6,
                    'payment' => $hendfs->sum('PAGO') ?? 0,
                ];
            }

            if (count($hrndfs) > 0) {
                $params['accrued']['HRNDFs'][] = [
                    'start_time' => Carbon::parse("$year-$month-01")->format('Y-m-01\TH:i:s'),
                    'end_time' => Carbon::parse("$year-$month-01")->format('Y-m-t\TH:i:s'),
                    'quantity' => (int) $hrndfs->sum('HORAS') ?? 0,
                    'percentage' => 7,
                    'payment' => $hrndfs->sum('PAGO') ?? 0,
                ];
            }

            /**
            if (count($common_vacation) > 0) {
                $params['accrued']['common_vacation'][] = [
                    'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'quantity' => (int) $common_vacation->sum('HORAS') / 8 ?? 0,
                    'payment' => $common_vacation->sum('PAGO') ?? 0,
                ];
            }

            if (count($paid_vacation) > 0) {
                $params['accrued']['paid_vacation'][] = [
                    'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'quantity' => (int) $paid_vacation->sum('HORAS') ?? 0,
                    'payment' => $paid_vacation->sum('PAGO') ?? 0,
                ];
            }*/

            if (count($service_bonus) > 0) {
                $params['accrued']['service_bonus'][] = [
                    'quantity' => (int) $service_bonus->sum('HORAS') ?? 0,
                    'payment' => $service_bonus->sum('PAGO') ?? 0,
                    'paymentNS' => '00.00',
                ];
            }

            /*
            if (count($severance) > 0) {
                $params['accrued']['severance'][] = [
                    'payment' => $severance->sum('PAGO') ?? 0,
                    'percentage' => $pay->where('CONCEPTO', '=', 110)->sum('HORAS') ?? 0,
                    'interest_payment' => $pay->where('CONCEPTO', '=', 110)->sum('PAGO') ?? 0,
                ];
            }*/

            if (count($work_disabilities) > 0) {
                $params['accrued']['work_disabilities'][] = [
                    'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'type' => 3,
                    'quantity' => $work_disabilities->sum('HORAS') ? round($work_disabilities->sum('HORAS') / 8, 0, PHP_ROUND_HALF_ODD) : 0,
                    'payment' => $work_disabilities->sum('PAGO') ?? 0,
                ];
            }

            if (count($maternity_leave) > 0) {
                $params['accrued']['maternity_leave'][] = [
                    'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'quantity' => $maternity_leave->sum('HORAS') ? round($maternity_leave->sum('HORAS') / 8, 0, PHP_ROUND_HALF_ODD) : 0,
                    'payment' => $maternity_leave->sum('PAGO') ?? 0,
                ];
            }

            if (count($paid_leave) > 0) {
                $params['accrued']['paid_leave'][] = [
                    'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'quantity' => $paid_leave->sum('HORAS') ? round($paid_leave->sum('HORAS') / 8, 0, PHP_ROUND_HALF_ODD) : 0,
                    'payment' => $paid_leave->sum('PAGO') ?? 0,
                ];
            }

            if (count($non_paid_leave) > 0) {
                $params['accrued']['non_paid_leave'][] = [
                    'start_date' => Carbon::parse("$year-$month-01")->format('Y-m-01'),
                    'end_date' => Carbon::parse("$year-$month-01")->format('Y-m-t'),
                    'quantity' => $non_paid_leave->sum('HORAS') ? round($non_paid_leave->sum('HORAS') / 8, 0, PHP_ROUND_HALF_ODD) : 0,
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
            $other_deductions = $pay->whereIn('CONCEPTO',  [350, 440]);
            $voluntary_pension = $pay->where('CONCEPTO', '=', 304);
            $withholding_at_source = $pay->whereIn('CONCEPTO',  [310, 311, 312]);
            $tax_liens = $pay->whereIn('CONCEPTO', [320, 321, 322]);
            $debt = $pay->whereIn('CONCEPTO', [152, 153]);
            $fondosp_deduction_SP = $pay->where('CONCEPTO', '=', 303);

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

            if (count($fondosp_deduction_SP) > 0) {
                $params['deductions']['fondosp_deduction_SP'] = $fondosp_deduction_SP->sum('DEDUCCIONES') ?? 0;
            }

            return [
                'params' => $params,
                'status' => true,
            ];
        } catch (\Exception $e) {
            return [
                'params' => null,
                'status' => false,
            ];
        }
    }

    /**
     * @param $entity
     * @return int
     */
    protected function last_consecutive($entity): int
    {
        $max_number = DB::table('payroll_documents')
            ->where('entity', '=', $entity)
            ->select(
                DB::raw('max(consecutive) as value')
            )->first()->value ?? ($entity === 'CIEV' ? 4076 : 3890);

        return intval($max_number + 1);
    }

    /**
     * @param  string  $type
     * @return int
     */
    protected function type_document(string $type): int
    {
        return match (trim($type)) {
            'C' => 1,
            'E' => 2,
            'T' => 3,
            'P' => 4,
            'PE' => 9,
            default => false,
        };
    }

    /**
     * @param $document_reference
     * @param  string  $entity
     * @return array
     */
    protected function generateParamsDestroy($document_reference, string $entity = 'CIEV'): array
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
            'consecutive' => $this->last_consecutive($entity),
            'payroll_period_id' => 4,
            'notes' => 'ELIMINAR',
        ];
    }

    /**
     * @param $year
     * @param $month
     * @param $start_period
     * @param $end_period
     * @param  string  $entity
     * @return void
     *
     * @throws GuzzleException
     */
    protected function sendDIAN($year, $month, $start_period, $end_period, string $entity = 'CIEV'): void
    {
        try {
            $list = \App\Models\PayrollDocument::where('entity', '=', $entity)
                ->where('year', '=', $year)
                ->where('month', '=', $month)
                ->where('start_period', '=', $start_period)
                ->where('end_period', '=', $end_period)
                ->whereIn('status', ['pending', 'failed'])
                ->get();

            if ($entity === 'CIEV') {
                $token = config('apidian.token');
                $entity_document = 890926617;
            } else {
                $token = config('apidian.token_goja');
                $entity_document = 900349726;
            }
            $url = 'http://192.168.1.44:8400/api';
            set_time_limit(0);

            $client = new Client(['base_uri' => $url]);

            $headers = [
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ];

            foreach ($list as $item) {
                $current_id = $item->id;
                $response_api = null;

                DB::beginTransaction();
                try {
                    switch ($item->type_operation) {
                        case 'destroy':
                        case 'adjust':
                            $response_api = $client->request('POST', "{$url}/ubl2.1/payroll-adjust-note", [
                                'headers' => $headers,
                                'json' => $item->payload,
                            ]);
                            break;
                        case 'payroll':
                            $response_api = $client->request('POST', "{$url}/ubl2.1/payroll", [
                                'headers' => $headers,
                                'json' => $item->payload,
                            ]);
                            break;
                    }

                    $resp = $response_api->getBody()->getContents();
                    $resp = is_string($resp)
                        ? json_decode($resp)
                        : $resp;

                    Log::error("DOCUMENT: $item->consecutive PAYLOAD: " .json_encode($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage));


                    PayrollLog::create([
                        'consecutive' => $item->consecutive,
                        'status' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid,
                        'statusCode' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusCode,
                        'ErrorMessage' => $resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string ?? '',
                        'cune' => $resp->cune,
                        'send_by' => Auth::id(),
                        'entity' => $item->entity,
                    ]);


                    if ($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid === 'true' || str_contains($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string, 'procesado anteriormente')) {
                        $doc = \App\Models\PayrollDocument::find($current_id);
                        $doc->status = 'success';
                        $doc->save();

                        /*
                        if ($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid === 'false' && str_contains($resp->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string, 'procesado anteriormente')) {
                            $document = PayrollDocument::where('identification_number', '=', $entity_document)
                                ->where('consecutive', '=', $item->consecutive)
                                ->firstOrFail();

                            $document->update([
                                'state_document_id' => 1,
                                'cune' => $resp->cune,
                            ]);
                        }
                        */
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    PayrollLog::create([
                        'consecutive' => $item->consecutive,
                        'status' => 'FAILED',
                        'statusCode' => $e->getCode(),
                        'ErrorMessage' => $e->getMessage(),
                        'cune' => 'NA',
                        'send_by' => Auth::id(),
                        'entity' => $entity,
                    ]);

                    $doc = \App\Models\PayrollDocument::find($current_id);
                    $doc->status = 'failed';
                    $doc->save();
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
