<?php

namespace App\Http\Controllers;

use App\Models\Dian\PayrollSettlement;
use App\Models\PayrollDocument;
use App\Traits\PayrollTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    use PayrollTrait;

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

        return Inertia::render('Applications/Payroll/Index', [
            'years' => $years,
        ]);
    }

    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function get_employees(Request $r): JsonResponse
    {
        $settlement = PayrollSettlement::where('AÑO', '=', $r->year)
            ->where('MES', '=', $r->month)
            ->whereBetween('PERIODO', [$r->start_period, $r->end_period])
            ->whereNotIn('CONCEPTO', [14, 110, 101, 102, 103, 105])
            ->selectRaw('IDENTIFICACION, EMPLEADO, SUM(PAGO) as PAGO, SUM(DEDUCCIONES) as DEDUCCIONES, SUM(PAGO) - SUM(DEDUCCIONES) as NETO')
            ->groupBy('IDENTIFICACION', 'EMPLEADO')
            ->get()->map(function ($row) use ($r) {
                $row->current_document = PayrollDocument::where('entity', '=', 'CIEV')
                    ->where('employee_document', '=', $row->IDENTIFICACION)
                    ->where('year', '=', $r->year)
                    ->where('month', '=', $r->month)
                    ->where('start_period', '=', $r->start_period)
                    ->where('end_period', '=', $r->end_period)
                    ->first();

                return $row;
            });

        return response()->json($settlement, 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function sendPayroll(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->employees as $employee) {
                if ($request->type_operation === 'payroll') {
                    $params = $this->generateParams(
                        $employee['IDENTIFICACION'],
                        $request->year,
                        $request->month,
                        $request->start_period,
                        $request->end_period,
                    );
                } elseif ($request->type_operation === 'adjust') {
                    $params = $this->generateParams(
                        $employee['IDENTIFICACION'],
                        $request->year,
                        $request->month,
                        $request->start_period,
                        $request->end_period,
                        $employee['document_reference']
                    );
                } else {
                    $params = $this->generateParamsDestroy($employee['document_reference']);
                }
                if (! $params['status']) {
                    continue;
                }

                $params = $params['params'];
                $exist_registry = PayrollDocument::where('employee_document', '=', $employee['IDENTIFICACION'])
                    ->where('entity', '=', 'CIEV')
                    ->where('year', '=', $request->year)
                    ->where('month', '=', $request->month)
                    ->where('start_period', '=', $request->start_period)
                    ->where('end_period', '=', $request->end_period)
                    ->first();

                if (!$exist_registry) {
                    PayrollDocument::create([
                        'employee_document' => $employee['IDENTIFICACION'],
                        'consecutive' => $params['consecutive'],
                        'entity' => 'CIEV',
                        'payload' => $params,
                        'year' => $request->year,
                        'month' => $request->month,
                        'start_period' => $request->start_period,
                        'end_period' => $request->end_period,
                        'status' => 'pending',
                        'type_operation' => $request->type_operation,
                        'document_reference' => $employee['document_reference'] ?? null,
                    ]);
                } else {
                    $params['consecutive'] = $exist_registry->consecutive;

                    $exist_registry->update([
                        'payload' => $params,
                    ]);
                }
            }
            DB::commit();
            $this->sendDIAN($request->year, $request->month, $request->start_period, $request->end_period, 'CIEV');

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        } catch (GuzzleException $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
