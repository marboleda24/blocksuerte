<?php

namespace App\Http\Controllers\ElectronicBilling\Exports;

use App\Http\Controllers\Controller;
use App\Models\Dian\Invoices;
use App\Models\Goja\Invoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use phpseclib3\System\SSH\Agent\Identity;

class InvoiceController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function index($entity): Response
    {
        return Inertia::render('Applications/ElectronicBilling/Exports/Invoices', [
            'entity' => $entity
        ]);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function search_by_date(Request $request, $entity): JsonResponse
    {
        $date = explode(' - ', $request->date);

        if ($entity === 'CIEV') {
            $data = Invoices::whereBetween('fecha', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->where('tipodoc', '=', 'CU')
                ->where('tipocliente', '=', 'EX')
                ->where('moneda', '=', 'USD')
                ->orderBy('numero', 'asc')
                ->get();

        } else {
            $data = Invoice::whereBetween('fecha', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->where('tipodoc', '=', 'CU')
                ->where('tipocliente', '=', 'EX')
                ->where('moneda', '=', 'USD')
                ->orderBy('numero', 'asc')
                ->get();

        }
        return response()->json($data, 200);


    }
}
