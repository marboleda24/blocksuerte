<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\Sell\SalesPerDayExport;
use App\Http\Controllers\Controller;
use App\Models\MAXInvoice;
use App\Models\MAXPGInvoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function foo\func;

class SalesReportController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function per_day($entity)
    {
        return Inertia::render('Applications/Report/Sales/SalesPerDay', [
            'entity' => $entity
        ]);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function per_day_get_documents(Request $request, $entity)
    {
        if ($entity === 'CIEV') {
            try {
                $date = explode(' - ', $request->date_range);

                $data = MAXInvoice::with('details')
                    ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                    ->where(function ($query) use ($request) {
                        if ($request->type === 'CI') {
                            $query->where('TIPOCLIENTE', '=', 'CI');
                        } elseif ($request->type === 'NATIONAL') {
                            $query->whereIn('TIPOCLIENTE', ['PN', 'RC']);
                        }
                    })->orderBy('NUMERO')->get();

                return response()->json($data, 200);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        } else {
            try {
                $date = explode(' - ', $request->date_range);

                $data = MAXPGInvoice::with('details')
                    ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                    ->where(function ($query) use ($request) {
                        if ($request->type === 'CI') {
                            $query->where('TIPOCLIENTE', '=', 'CI');
                        } elseif ($request->type === 'NATIONAL') {
                            $query->whereIn('TIPOCLIENTE', ['PN', 'RC']);
                        }
                    })->orderBy('NUMERO')->get();

                return response()->json($data, 200);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    /**
     * @param Request $request
     * @param $entity
     * @return BinaryFileResponse
     */
    public function per_day_download_report(Request $request, $entity)
    {
        return Excel::download(new SalesPerDayExport($request->type, $request->date_range, $entity), 'file.xlsx');
    }

    /**
     * @return Response
     */
    public function ov_pending_per_product()
    {
        return Inertia::render('Applications/Report/Sales/ovPendingPerProduct');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ov_pending_per_product_search(Request $request)
    {
        $query = DB::connection('MAX')
            ->table('CIEV_V_OVAbiertas')
            ->where('REFERENCIA', '=', $request->code)->get();

        return response()->json($query, 200);
    }

}
