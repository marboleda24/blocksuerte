<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use App\Models\MAXInvoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingPerDayController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:queries.billing-per-day');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = auth()->user()->hasPermissionTo('queries.billing-per-day.all') || auth()->user()->hasRole('super-admin')
            ? MAXInvoice::with('details')
                ->where('TIPODOC', '=', 'CU')
                ->whereBetween('FECHA', [Carbon::now()->format('Y-m-d 00:00:00'), Carbon::now()->format('Y-m-d 23:59:59')])
                ->get()
            : MAXInvoice::with('details')
                ->where('TIPODOC', '=', 'CU')
                ->where('CODVENDEDOR', '=', auth()->user()->vendor_code)
                ->whereBetween('FECHA', [Carbon::now()->format('Y-m-d 00:00:00'), Carbon::now()->format('Y-m-d 23:59:59')])
                ->get();

        return Inertia::render('Applications/Queries/BillingPerDay', [
            'data' => $data,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function invoices(Request $request): JsonResponse
    {
        $date = explode(' - ', $request->date);

        $data = MAXInvoice::with('details')
            ->where('TIPODOC', '=', 'CU')
            ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
            ->orderBy('NUMERO')
            ->get();

        return response()->json($data, 200);
    }
}
