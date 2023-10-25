<?php

namespace App\Http\Controllers;

use App\Exports\SecurityHealthWork\AbsenteeismExport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use QuickChart;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SecurityHealthWorkController extends Controller
{
    /**
     * @return Response
     */
    public function sociodemographic()
    {
        $employees = DB::connection('DMS')
            ->table('V_CIEV_Sociodemografia')
            ->get();

        return Inertia::render('Applications/SecurityHealthWork/Sociodemographic', [
            'employees' => $employees
        ]);
    }

    /**
     * @param $document
     * @return Response
     */
    public function work_absenteeism($document)
    {
        $medical_disabilities = DB::connection('DMS')
            ->table('V_CIEV_Incapacidades')
            ->where('nit', '=', $document)
            ->orderBy('fecha_inicial', 'desc')
            ->get();

        $employee = DB::connection('DMS')
            ->table('V_CIEV_Sociodemografia')
            ->where('IDENTIFICACION', '=', $document)
            ->first();

        return Inertia::render('Applications/SecurityHealthWork/WorkAbsenteeism', [
            'disabilities' => $medical_disabilities,
            'employee' => $employee
        ]);
    }

    /**
     * @return Response
     */
    public function reports()
    {
        $inability = DB::connection('DMS')
            ->table('V_CIEV_HorasHombreTrabajadas')
            ->where('ano', '>', 2017)
            ->groupBy('ano', 'mes')
            ->orderBy('ano', 'asc')
            ->orderBy('mes', 'asc')
            ->whereIn('concepto', [46, 47, 48, 50, 51, 52])
            ->select('ano', 'mes', DB::raw('sum(horas) as hours'))
            ->get();

        $hour_hw = DB::connection('DMS')
            ->table('V_CIEV_HorasHombreTrabajadas')
            ->where('ano', '>', 2017)
            ->groupBy('ano', 'mes')
            ->orderBy('ano', 'asc')
            ->orderBy('mes', 'asc')
            ->whereIn('concepto', [1, 12, 15, 21, 22, 31, 32, 41])
            ->select('ano', 'mes', DB::raw('sum(horas) as hours'))
            ->get();

        $accw = DB::connection('DMS')
            ->table('V_CIEV_HorasHombreTrabajadas')
            ->where('ano', '>', 2017)
            ->groupBy('ano', 'mes')
            ->orderBy('ano', 'asc')
            ->orderBy('mes', 'asc')
            ->whereIn('concepto', [49, 54])
            ->select('ano', 'mes', DB::raw('sum(horas) as hours'))
            ->get();


        $count = DB::connection('DMS')
            ->table('V_CIEV_Conteo_Personal')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $hours_concept_month = (object)[
            'hour_hw' => $hour_hw->groupBy('ano'),
            'inability' => $inability->groupBy('ano'),
            'accw' => $accw->groupBy('ano'),
            'count' => $count->groupBy('year')
        ];

        return Inertia::render('Applications/SecurityHealthWork/Report', [
            'hours_concept_month' => $hours_concept_month,
        ]);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function download_report(Request $request)
    {
        $yearsList = $request->years;
        $years = $this->betweenYear(count($yearsList) > 0 ? $yearsList : [Carbon::today()->year]);

        $chart_inability = new QuickChart(array('width' => 1600, 'height' => 800));
        $chart_inability->setConfig($request->inability_chart);
        $chart_inability->setVersion(4);
        $chart_inability_url = $chart_inability->getShortUrl();

        $chart_work_center = new QuickChart(array('width' => 1600, 'height' => 800));
        $chart_work_center->setConfig($request->work_center_chart);
        $chart_inability->setVersion(4);
        $chart_work_center_url = $chart_work_center->getShortUrl();

        $chart_diagnostic = new QuickChart(array('width' => 1600, 'height' => 800));
        $chart_diagnostic->setConfig($request->diagnostic_chart);
        $chart_inability->setVersion(4);
        $chart_diagnostic_url = $chart_diagnostic->getShortUrl();

        $reports = (object)[
            'inability' => $this->inability_report($request, true),
            'work_center' => $this->work_center_report($request, true),
            'diagnostic' => $this->diagnostic_report($request, true)
        ];

        $charts = (object)[
            'inability' => $chart_inability_url,
            'work_center' => $chart_work_center_url,
            'diagnostic' => $chart_diagnostic_url,
        ];

        $pdf = PDF::loadView('pdfs.SecurityHealthWork.report', compact('reports', 'charts', 'years'));
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('file.pdf');

    }

    /**
     * @param Request $request
     * @param bool $pdf
     * @return JsonResponse|object
     */
    public function inability_report(Request $request, bool $pdf = false)
    {
        $yearsList = $request->years;
        $years = $this->betweenYear(count($yearsList) > 0 ? $yearsList : [Carbon::today()->year]);

        $data = DB::connection('DMS')
            ->table('V_CIEV_Incapacidades')
            ->select(DB::raw('count(*) as quantity'), 'ConceptoIncapacidad')
            ->whereBetween('fecha_inicial', [$years->start, $years->end])
            ->groupBy('ConceptoIncapacidad')
            ->get()
            ->toArray();

        if ($pdf) {
            return (object)[
                'labels' => array_column($data, 'ConceptoIncapacidad'),
                'values' => array_column($data, 'quantity')
            ];
        } else {
            return response()->json([
                'labels' => array_column($data, 'ConceptoIncapacidad'),
                'values' => array_column($data, 'quantity')
            ], 200);
        }
    }

    /**
     * @param array $years
     * @return object
     */
    protected function betweenYear(array $years)
    {
        $minYear = min($years);
        $maxYear = max($years);

        $startDayOfYear = Carbon::create($minYear, 1, 1)->startOfYear();
        $endDayOfYear = Carbon::create($maxYear, 12, 31)->endOfYear();

        return (object)[
            'start' => $startDayOfYear->toDateString(),
            'end' => $endDayOfYear->toDateString()
        ];
    }

    /**
     * @param Request $request
     * @param bool $pdf
     * @return JsonResponse|object
     */
    public function work_center_report(Request $request, bool $pdf = false)
    {
        $yearsList = $request->years;
        $years = $this->betweenYear(count($yearsList) > 0 ? $yearsList : [Carbon::today()->year]);

        $data = DB::connection('DMS')
            ->table('V_CIEV_Incapacidades')
            ->select(DB::raw('sum(dias_incap) as quantity'), 'CENTRO_TRABAJO')
            ->whereBetween('fecha_inicial', [$years->start, $years->end])
            ->groupBy('CENTRO_TRABAJO')
            ->get()
            ->toArray();

        if ($pdf) {
            return (object)[
                'labels' => array_column($data, 'CENTRO_TRABAJO'),
                'values' => array_column($data, 'quantity')
            ];
        } else {
            return response()->json([
                'labels' => array_column($data, 'CENTRO_TRABAJO'),
                'values' => array_column($data, 'quantity')
            ], 200);
        }


    }

    /**
     * @param Request $request
     * @param bool $pdf
     * @return object
     */
    public function diagnostic_report(Request $request, bool $pdf = false)
    {
        $yearsList = $request->years;
        $years = $this->betweenYear(count($yearsList) > 0 ? $yearsList : [Carbon::today()->year]);

        $data = DB::connection('DMS')
            ->table('V_CIEV_Incapacidades')
            ->select(DB::raw('count(*) as quantity'), 'SVE_INTERES')
            ->whereBetween('fecha_inicial', [$years->start, $years->end])
            ->groupBy('SVE_INTERES')
            ->get()
            ->toArray();

        $labels = array_column($data, 'SVE_INTERES');
        $pos = array_search(null, $labels);

        if ($pos !== false) {
            $labels[$pos] = "N/A";
        }

        if ($pdf) {
            return (object)[
                'labels' => $labels,
                'values' => array_column($data, 'quantity')
            ];
        } else {
            return response()->json([
                'labels' => $labels,
                'values' => array_column($data, 'quantity')
            ], 200);
        }


    }

    /**
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function export_absenteeism(Request $request){
        try {
            $date = explode(' - ', $request->date_range);
            return Excel::download(new AbsenteeismExport(Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')), 'report.xlsx');
        }catch (Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
}
