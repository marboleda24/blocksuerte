<?php

namespace App\Http\Controllers\Goja;

use App\Exports\Goja\SalesPerProductExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * @return Response
     */
    public function sales_per_product()
    {
        return Inertia::render('Applications/Goja/SalesPerProduct');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sales_per_product_search(Request $request)
    {
        $result = DB::connection('MAXPG')
            ->table('PG_V_FE_FacturasDetalladas')
            ->whereBetween('FECHA', [Carbon::parse($request->startDate)->format('Y-d-m'), Carbon::parse($request->endDate)->format('Y-d-m')])
            ->select('CodigoProducto', 'DescripcionProducto', DB::raw('SUM(Cantidad)  AS quantity'), DB::raw('SUM(TotalItem) AS total'))
            ->groupBy('CodigoProducto', 'DescripcionProducto')
            ->get();

        return response()->json($result, 200);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function sales_per_product_download(Request $request)
    {
        return Excel::download(new SalesPerProductExport($request->startDate, $request->endDate), 'maintenance-requests.xlsx');
    }
}
